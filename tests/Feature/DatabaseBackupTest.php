<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PDO;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);
    }

    public function test_can_view_database_backup_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.database'));
        $response->assertStatus(200);
        $response->assertSee('Cadangan & Pemulihan Data');
    }

    public function test_can_create_local_snapshot(): void
    {
        $response = $this->actingAs($this->user)->post(route('database.create-snapshot'));
        $response->assertRedirect(route('settings.database'));
        $response->assertSessionHas('success');

        $backupDir = storage_path('app/backups');
        $this->assertTrue(File::isDirectory($backupDir));
    }

    public function test_can_download_current_database(): void
    {
        $response = $this->actingAs($this->user)->get(route('database.download-current'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/x-sqlite3');
    }

    public function test_rejects_invalid_file_upload_for_restore(): void
    {
        $fakeFile = UploadedFile::fake()->createWithContent('invalid.sqlite', 'this is not a sqlite file');

        $response = $this->actingAs($this->user)->post(route('database.restore-upload'), [
            'database_file' => $fakeFile,
        ]);

        $response->assertSessionHasErrors(['error']);
    }

    public function test_accepts_valid_sqlite_backup_upload(): void
    {
        // Buat file sqlite mini yang valid dengan tabel users, students, academic_years
        $tempPath = tempnam(sys_get_temp_dir(), 'test_sqlite_');
        $pdo = new PDO("sqlite:{$tempPath}");
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY);');
        $pdo->exec('CREATE TABLE students (id INTEGER PRIMARY KEY);');
        $pdo->exec('CREATE TABLE academic_years (id INTEGER PRIMARY KEY);');
        $pdo = null;

        $validFile = new UploadedFile($tempPath, 'valid_backup.sqlite', 'application/x-sqlite3', null, true);

        $response = $this->actingAs($this->user)->post(route('database.restore-upload'), [
            'database_file' => $validFile,
        ]);

        $response->assertRedirect(route('settings.database'));
        $response->assertSessionHas('success');

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}
