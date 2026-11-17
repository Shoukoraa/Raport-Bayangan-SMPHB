<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman Portal Utama (Landing Page Pilihan Workspace)
     */
    public function portal(): View
    {
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('portal', compact('totalStudents', 'totalSubjects', 'activeYear'));
    }

    /**
     * Kompatibilitas route dashboard default Breeze
     */
    public function index(): View
    {
        return $this->portal();
    }

    /**
     * Dashboard Khusus Workspace UTS (Raport Bayangan)
     */
    public function uts(): View
    {
        $totalStudents = Student::count();
        $totalSubjects = Subject::where('is_active', true)->count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $activeYearId = $activeYear?->id;
        $gradedStudentsCount = Student::whereHas('grades', function ($query) use ($activeYearId) {
            if ($activeYearId) {
                $query->where('academic_year_id', $activeYearId);
            }
        })->count();

        return view('uts.dashboard', compact('totalStudents', 'totalSubjects', 'activeYear', 'gradedStudentsCount'));
    }

    /**
     * Dashboard Khusus Workspace UAS (Raport Resmi)
     */
    public function uas(): View
    {
        $totalStudents = Student::count();
        $totalSubjects = Subject::where('is_active', true)->count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $activeYearId = $activeYear?->id;
        $uasGradedStudentsCount = Student::whereHas('uasGrades', function ($query) use ($activeYearId) {
            if ($activeYearId) {
                $query->where('academic_year_id', $activeYearId);
            }
        })->count();

        return view('uas.dashboard', compact('totalStudents', 'totalSubjects', 'activeYear', 'uasGradedStudentsCount'));
    }
}
