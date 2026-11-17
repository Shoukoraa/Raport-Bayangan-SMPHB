<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UasReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // -------------------------------------------------------------
    // 1. PORTAL UTAMA (Dual-Workspace Gateway)
    // -------------------------------------------------------------
    Route::get('/portal', [DashboardController::class, 'portal'])->name('portal');
    Route::get('/dashboard', [DashboardController::class, 'portal'])->name('dashboard');

    // -------------------------------------------------------------
    // 2. WORKSPACE UTS (Sistem Raport Bayangan)
    // -------------------------------------------------------------
    Route::prefix('uts')->name('uts.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'uts'])->name('dashboard');
        Route::get('/settings/school', [SchoolSettingController::class, 'editUts'])->name('settings.school');
    });

    // Raport Bayangan UTS (Halaman Kerja Terpadu & Input Nilai)
    Route::get('/reports', [ReportCardController::class, 'index'])->name('reports.index');
    Route::get('/reports/print-all', [ReportCardController::class, 'printAll'])->name('reports.print-all');
    Route::get('/reports/{student}/preview', [ReportCardController::class, 'preview'])->name('reports.preview');
    Route::get('/reports/{student}/excel', [ReportCardController::class, 'excel'])->name('reports.excel');

    // Nilai UTS
    Route::get('/grades', [ReportCardController::class, 'index'])->name('grades.index');
    Route::get('/grades/{student}', [StudentGradeController::class, 'edit'])->name('grades.edit');
    Route::post('/grades/{student}', [StudentGradeController::class, 'store'])->name('grades.store');

    // -------------------------------------------------------------
    // 3. WORKSPACE UAS (Sistem Raport Resmi e-Rapor)
    // -------------------------------------------------------------
    Route::prefix('uas')->name('uas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'uas'])->name('dashboard');
        Route::get('/settings/school', [SchoolSettingController::class, 'editUas'])->name('settings.school');
    });

    // Raport UAS / PAS (Kurikulum Merdeka)
    Route::get('/uas-reports', [UasReportController::class, 'index'])->name('uas-reports.index');
    Route::get('/uas-reports/print-all', [UasReportController::class, 'printAll'])->name('uas-reports.print-all');
    Route::get('/uas-reports/{student}/edit', [UasReportController::class, 'edit'])->name('uas-reports.edit');
    Route::post('/uas-reports/{student}', [UasReportController::class, 'store'])->name('uas-reports.store');
    Route::get('/uas-reports/{student}/preview', [UasReportController::class, 'preview'])->name('uas-reports.preview');

    // -------------------------------------------------------------
    // 4. DATA MASTER BERSAMA & PENGATURAN UMUM
    // -------------------------------------------------------------
    // Students
    Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::resource('students', StudentController::class);

    // Subjects
    Route::post('/subjects/reorder', [SubjectController::class, 'reorder'])->name('subjects.reorder');
    Route::post('/subjects/{subject}/move-up', [SubjectController::class, 'moveUp'])->name('subjects.move-up');
    Route::post('/subjects/{subject}/move-down', [SubjectController::class, 'moveDown'])->name('subjects.move-down');
    Route::resource('subjects', SubjectController::class);

    // Academic Years & Semester Management
    Route::post('/academic-years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
    Route::resource('academic-years', AcademicYearController::class);

    // Settings (School Letterhead)
    Route::get('/settings/school', [SchoolSettingController::class, 'edit'])->name('settings.school');
    Route::put('/settings/school', [SchoolSettingController::class, 'update'])->name('settings.school.update');

    // Database Backup & Recovery (SQLite)
    Route::get('/settings/database', [DatabaseBackupController::class, 'index'])->name('settings.database');
    Route::get('/settings/database/backup', [DatabaseBackupController::class, 'downloadCurrent'])->name('database.download-current');
    Route::post('/settings/database/create-snapshot', [DatabaseBackupController::class, 'createSnapshot'])->name('database.create-snapshot');
    Route::get('/settings/database/download-snapshot/{filename}', [DatabaseBackupController::class, 'downloadSnapshot'])->name('database.download-snapshot');
    Route::post('/settings/database/restore-snapshot', [DatabaseBackupController::class, 'restoreSnapshot'])->name('database.restore-snapshot');
    Route::post('/settings/database/restore-upload', [DatabaseBackupController::class, 'restoreUpload'])->name('database.restore-upload');
    Route::delete('/settings/database/delete-snapshot/{filename}', [DatabaseBackupController::class, 'deleteSnapshot'])->name('database.delete-snapshot');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
