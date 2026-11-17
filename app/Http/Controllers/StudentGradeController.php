<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentGradeController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('name')->orderByDesc('semester')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($activeYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Student::with(['classroom', 'academicYear'])
            ->withCount(['grades as current_grades_count' => function ($q) use ($selectedYearId) {
                if ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                }
            }]);

        if ($statusFilter !== 'all' && ! empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();

        return view('grades.index', compact('students', 'academicYears', 'selectedYear', 'activeYear', 'statusFilter', 'search'));
    }

    public function edit(Request $request, Student $student): View
    {
        $subjects = Subject::orderBy('order_number')->get();
        $academicYears = AcademicYear::orderByDesc('name')->orderByDesc('semester')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $yearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($student->academic_year_id ?? $activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;

        $grades = StudentGrade::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('subject_id');

        $attendance = Attendance::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear?->id,
            ],
            [
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'note' => 'Tetap semangat belajar ya nak!',
            ]
        );

        return view('grades.edit', compact('student', 'subjects', 'grades', 'attendance', 'academicYear', 'academicYears', 'activeYear'));
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId = $request->filled('academic_year_id')
            ? (int) $request->input('academic_year_id')
            : ($student->academic_year_id ?? $activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;

        // Validasi input nilai & kehadiran
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'grades.*.pr' => 'nullable|numeric|min:0|max:100',
            'grades.*.uh' => 'nullable|numeric|min:0|max:100',
            'grades.*.tertulis' => 'nullable|numeric|min:0|max:100',
            'grades.*.praktek' => 'nullable|numeric|min:0|max:100',
            'attendance.sakit' => 'nullable|integer|min:0',
            'attendance.izin' => 'nullable|integer|min:0',
            'attendance.alpa' => 'nullable|integer|min:0',
            'attendance.note' => 'nullable|string|max:500',
        ]);

        // Simpan nilai per mata pelajaran untuk periode ini
        if ($request->has('grades')) {
            foreach ($request->grades as $subjectId => $gradeData) {
                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'pr' => $gradeData['pr'] ?? null,
                        'uh' => $gradeData['uh'] ?? null,
                        'tertulis' => $gradeData['tertulis'] ?? null,
                        'praktek' => $gradeData['praktek'] ?? null,
                    ]
                );
            }
        }

        // Simpan kehadiran untuk periode ini
        if ($request->has('attendance')) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'sakit' => $request->attendance['sakit'] ?? 0,
                    'izin' => $request->attendance['izin'] ?? 0,
                    'alpa' => $request->attendance['alpa'] ?? 0,
                    'note' => $request->attendance['note'] ?? null,
                ]
            );
        }

        if ($request->action == 'preview') {
            return redirect()->route('reports.preview', [
                'student' => $student->id,
                'academic_year_id' => $academicYear->id,
            ]);
        }

        return redirect()->route('grades.edit', [
            'student' => $student->id,
            'academic_year_id' => $academicYear->id,
        ])->with('success', "Data nilai periode {$academicYear->full_name} berhasil disimpan.");
    }
}
