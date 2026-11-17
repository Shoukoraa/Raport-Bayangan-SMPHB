<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use PDO;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class DatabaseBackupController extends Controller
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');

        if (! File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    protected function getDbPath(): string
    {
        if (app()->environment('testing')) {
            $testDbDir = storage_path('framework/testing');
            if (! File::isDirectory($testDbDir)) {
                File::makeDirectory($testDbDir, 0755, true);
            }
            $testDb = $testDbDir.DIRECTORY_SEPARATOR.'test_database.sqlite';
            if (! File::exists($testDb)) {
                File::put($testDb, '');
            }

            return $testDb;
        }

        $configPath = config('database.connections.sqlite.database');
        if ($configPath && $configPath !== ':memory:' && File::exists($configPath)) {
            return $configPath;
        }

        return database_path('database.sqlite');
    }

    public function index(): View
    {
        $dbPath = $this->getDbPath();
        $dbExists = File::exists($dbPath);
        $dbSize = $dbExists ? $this->formatBytes(File::size($dbPath)) : '0 B';
        $dbModified = $dbExists ? date('d M Y H:i:s', File::lastModified($dbPath)) : '-';

        $stats = [
            'users' => User::count(),
            'students' => Student::count(),
            'subjects' => Subject::count(),
            'academic_years' => AcademicYear::count(),
            'grades' => StudentGrade::count(),
        ];

        // Daftar snapshot lokal yang tersimpan di storage/app/backups
        $backupFiles = [];
        if (File::isDirectory($this->backupDir)) {
            $files = File::files($this->backupDir);
            foreach ($files as $file) {
                if (in_array(strtolower($file->getExtension()), ['sqlite', 'db', 'bak'])) {
                    $backupFiles[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('d M Y H:i:s', $file->getMTime()),
                        'timestamp' => $file->getMTime(),
                        'is_safety' => str_starts_with($file->getFilename(), 'pre_restore_safety_backup_'),
                    ];
                }
            }

            usort($backupFiles, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        }

        return view('settings.database', compact('dbExists', 'dbSize', 'dbModified', 'stats', 'backupFiles'));
    }

    public function downloadCurrent(): BinaryFileResponse|RedirectResponse
    {
        $dbPath = $this->getDbPath();
        if (! File::exists($dbPath)) {
            return back()->withErrors(['error' => 'File database SQLite tidak ditemukan.']);
        }

        $filename = 'SMPHB_Raport_Backup_'.date('Y-m-d_H-i-s').'.sqlite';

        return response()->download($dbPath, $filename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    public function createSnapshot(): RedirectResponse
    {
        $dbPath = $this->getDbPath();
        if (! File::exists($dbPath)) {
            return back()->withErrors(['error' => 'File database SQLite tidak ditemukan.']);
        }

        $snapshotName = 'snapshot_'.date('Y-m-d_H-i-s').'.sqlite';
        $destination = $this->backupDir.DIRECTORY_SEPARATOR.$snapshotName;

        File::copy($dbPath, $destination);

        return redirect()->route('settings.database')->with('success', "Snapshot backup lokal '{$snapshotName}' berhasil dibuat dan disimpan di server.");
    }

    public function downloadSnapshot(string $filename): BinaryFileResponse|RedirectResponse
    {
        $safeFilename = basename($filename);
        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$safeFilename;

        if (! File::exists($filePath)) {
            return back()->withErrors(['error' => 'File snapshot tidak ditemukan di server.']);
        }

        return response()->download($filePath, $safeFilename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    public function deleteSnapshot(string $filename): RedirectResponse
    {
        $safeFilename = basename($filename);
        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$safeFilename;

        if (File::exists($filePath)) {
            File::delete($filePath);

            return redirect()->route('settings.database')->with('success', "Snapshot '{$safeFilename}' berhasil dihapus.");
        }

        return back()->withErrors(['error' => 'File snapshot tidak ditemukan.']);
    }

    public function restoreSnapshot(Request $request): RedirectResponse
    {
        $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $safeFilename = basename($request->filename);
        $sourcePath = $this->backupDir.DIRECTORY_SEPARATOR.$safeFilename;

        if (! File::exists($sourcePath)) {
            return back()->withErrors(['error' => 'File snapshot sumber tidak ditemukan.']);
        }

        // Validasi SQLite file
        if (! $this->isValidSqliteFile($sourcePath, $errorMessage)) {
            return back()->withErrors(['error' => "Restorasi dibatalkan: {$errorMessage}"]);
        }

        // Buat backup pengaman otomatis sebelum ditimpa
        $this->createSafetyBackup();

        // Timpa database
        File::copy($sourcePath, $this->getDbPath());

        // Bersihkan cache aplikasi agar data langsung tersegarkan
        $this->clearAppCaches();

        return redirect()->route('settings.database')->with('success', "Database SQLite berhasil dipulihkan (recovery) dari snapshot '{$safeFilename}'! Salinan pengaman database sebelumnya juga telah disimpan otomatis.");
    }

    public function restoreUpload(Request $request): RedirectResponse
    {
        $request->validate([
            'database_file' => ['required', 'file', 'max:102400'], // max 100MB
        ], [
            'database_file.required' => 'Pilih file database SQLite (.sqlite / .db) yang ingin dipulihkan.',
            'database_file.max' => 'Ukuran file maksimal adalah 100 MB.',
        ]);

        $file = $request->file('database_file');
        $tempPath = $file->getRealPath();

        // Validasi struktur & integritas SQLite
        if (! $this->isValidSqliteFile($tempPath, $errorMessage)) {
            return back()->withErrors(['error' => "File tidak valid untuk restorasi: {$errorMessage}"]);
        }

        // Buat backup pengaman otomatis sebelum ditimpa
        $this->createSafetyBackup();

        // Simpan juga salinan file upload ini ke dalam folder backup
        $savedUploadName = 'uploaded_restore_'.date('Y-m-d_H-i-s').'_'.basename($file->getClientOriginalName());
        File::copy($tempPath, $this->backupDir.DIRECTORY_SEPARATOR.$savedUploadName);

        // Timpa database aktif
        File::copy($tempPath, $this->getDbPath());

        // Bersihkan cache aplikasi
        $this->clearAppCaches();

        return redirect()->route('settings.database')->with('success', 'Database SQLite berhasil dipulihkan dari file upload Anda! Data sistem telah diperbarui dan snapshot pengaman telah dibuat.');
    }

    protected function createSafetyBackup(): void
    {
        $dbPath = $this->getDbPath();
        if (File::exists($dbPath)) {
            $safetyName = 'pre_restore_safety_backup_'.date('Y-m-d_H-i-s').'.sqlite';
            File::copy($dbPath, $this->backupDir.DIRECTORY_SEPARATOR.$safetyName);
        }
    }

    protected function isValidSqliteFile(string $filePath, ?string &$errorMessage = null): bool
    {
        if (! File::exists($filePath) || File::size($filePath) < 100) {
            $errorMessage = 'File kosong atau berukuran terlalu kecil.';

            return false;
        }

        // Periksa 16-byte SQLite header string: 'SQLite format 3'
        $header = file_get_contents($filePath, false, null, 0, 16);
        if (! str_starts_with($header, "SQLite format 3\000")) {
            $errorMessage = 'Header file bukan format database SQLite 3 yang valid.';

            return false;
        }

        // Uji query tabel-tabel utama
        try {
            $pdo = new PDO("sqlite:{$filePath}");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

            $requiredTables = ['users', 'students', 'academic_years'];
            foreach ($requiredTables as $table) {
                if (! in_array($table, $tables)) {
                    $errorMessage = "Tabel penting '{$table}' tidak ditemukan di dalam database ini. Pastikan file adalah database Raport SMPHB.";

                    return false;
                }
            }
        } catch (Throwable $e) {
            $errorMessage = 'Terjadi kesalahan saat memeriksa integritas database SQLite: '.$e->getMessage();

            return false;
        }

        return true;
    }

    protected function clearAppCaches(): void
    {
        try {
            Artisan::call('optimize:clear');
        } catch (Throwable $e) {
            // Ignore cache clear error
        }
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
