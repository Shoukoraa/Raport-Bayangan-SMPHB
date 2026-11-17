<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\StudentGrade;
use App\Models\StudentUasGrade;
use App\Models\StudentUasNote;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UasReportController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('name')->orderByDesc('semester')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($activeYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;
        $statusFilter = $request->query('status', 'Aktif');
        $search = $request->query('search');

        $query = Student::with(['classroom', 'academicYear'])
            ->withCount(['uasGrades as current_uas_grades_count' => function ($q) use ($selectedYearId) {
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

        return view('uas_reports.index', compact('students', 'academicYears', 'selectedYear', 'activeYear', 'statusFilter', 'search'));
    }

    public function edit(Request $request, Student $student): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
        $academicYears = AcademicYear::orderByDesc('name')->orderByDesc('semester')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $yearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($student->academic_year_id ?? $activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;

        // Ambil nilai UAS yang sudah ada
        $uasGrades = StudentUasGrade::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('subject_id');

        // Ambil nilai UTS (bayangan) sebagai acuan/referensi guru
        $utsGrades = StudentGrade::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('subject_id');

        // Ekskul
        $extracurriculars = StudentExtracurricular::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->orderBy('order_number')
            ->get();

        // Catatan UAS & P5
        $uasNote = StudentUasNote::firstOrNew(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear?->id,
            ],
            [
                'fase' => 'D',
                'kokurikuler_deskripsi' => '',
                'catatan_wali_kelas' => '',
                'tanggal_raport' => 'Karawang, 22 Desember 2026',
            ]
        );

        // Absensi
        $attendance = Attendance::firstOrNew(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear?->id,
            ],
            [
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
            ]
        );

        return view('uas_reports.edit', compact(
            'student',
            'subjects',
            'uasGrades',
            'utsGrades',
            'extracurriculars',
            'uasNote',
            'attendance',
            'academicYear',
            'academicYears',
            'activeYear'
        ));
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId = $request->filled('academic_year_id')
            ? (int) $request->input('academic_year_id')
            : ($student->academic_year_id ?? $activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'grades.*.nilai_akhir' => 'nullable|numeric|min:0|max:100',
            'grades.*.capaian_tertinggi' => 'nullable|string|max:1000',
            'grades.*.capaian_perlu_peningkatan' => 'nullable|string|max:1000',
            'attendance.sakit' => 'nullable|integer|min:0',
            'attendance.izin' => 'nullable|integer|min:0',
            'attendance.alpa' => 'nullable|integer|min:0',
            'note.fase' => 'nullable|string|max:10',
            'note.kokurikuler_deskripsi' => 'nullable|string',
            'note.catatan_wali_kelas' => 'nullable|string',
            'note.tanggal_raport' => 'nullable|string|max:100',
        ]);

        // Simpan nilai UAS
        if ($request->has('grades')) {
            foreach ($request->grades as $subjectId => $gradeData) {
                StudentUasGrade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'nilai_akhir' => $gradeData['nilai_akhir'] !== null && $gradeData['nilai_akhir'] !== '' ? (float) $gradeData['nilai_akhir'] : null,
                        'capaian_tertinggi' => $gradeData['capaian_tertinggi'] ?? null,
                        'capaian_perlu_peningkatan' => $gradeData['capaian_perlu_peningkatan'] ?? null,
                    ]
                );
            }
        }

        // Simpan Ekstrakurikuler
        StudentExtracurricular::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->delete();

        if ($request->has('ekskul') && is_array($request->ekskul)) {
            $order = 1;
            foreach ($request->ekskul as $item) {
                if (! empty($item['kegiatan'])) {
                    StudentExtracurricular::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYear->id,
                        'kegiatan' => $item['kegiatan'],
                        'predikat' => $item['predikat'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                        'order_number' => $order++,
                    ]);
                }
            }
        }

        // Simpan Catatan UAS & P5
        StudentUasNote::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'fase' => $request->input('note.fase', 'D'),
                'kokurikuler_deskripsi' => $request->input('note.kokurikuler_deskripsi'),
                'catatan_wali_kelas' => $request->input('note.catatan_wali_kelas'),
                'tanggal_raport' => $request->input('note.tanggal_raport', 'Karawang, 22 Desember 2026'),
            ]
        );

        // Simpan Absensi
        if ($request->has('attendance')) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'sakit' => $request->input('attendance.sakit', 0),
                    'izin' => $request->input('attendance.izin', 0),
                    'alpa' => $request->input('attendance.alpa', 0),
                ]
            );
        }

        if ($request->action == 'preview') {
            return redirect()->route('uas-reports.preview', [
                'student' => $student->id,
                'academic_year_id' => $academicYear->id,
            ]);
        }

        return redirect()->route('uas-reports.edit', [
            'student' => $student->id,
            'academic_year_id' => $academicYear->id,
        ])->with('success', "Data Raport UAS {$student->name} berhasil disimpan.");
    }

    public function preview(Request $request, Student $student): View
    {
        $student->load(['classroom']);
        $activeYear = AcademicYear::where('is_active', true)->first();

        $yearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($student->academic_year_id ?? $activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();

        $uasGrades = StudentUasGrade::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('subject_id');

        $extracurriculars = StudentExtracurricular::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->orderBy('order_number')
            ->get();

        $uasNote = StudentUasNote::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->first();

        $attendance = Attendance::where('student_id', $student->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->first();

        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'tanggal_raport' => 'Karawang, 22 Desember 2026',
        ]);

        return view('uas_reports.preview', compact(
            'student',
            'school',
            'academicYear',
            'subjects',
            'uasGrades',
            'extracurriculars',
            'uasNote',
            'attendance'
        ));
    }

    public function printAll(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId = $request->filled('academic_year_id')
            ? (int) $request->query('academic_year_id')
            : ($activeYear?->id);

        $academicYear = AcademicYear::find($yearId) ?? $activeYear;
        $statusFilter = $request->query('status', 'Aktif');

        $query = Student::with(['classroom']);
        if ($statusFilter !== 'all' && ! empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }
        $students = $query->get();
        $studentIds = $students->pluck('id');

        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();

        $allUasGrades = StudentUasGrade::whereIn('student_id', $studentIds)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->groupBy('student_id');

        $allEkskuls = StudentExtracurricular::whereIn('student_id', $studentIds)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->orderBy('order_number')
            ->get()
            ->groupBy('student_id');

        $allNotes = StudentUasNote::whereIn('student_id', $studentIds)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('student_id');

        $allAttendances = Attendance::whereIn('student_id', $studentIds)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->keyBy('student_id');

        foreach ($students as $student) {
            $student->setRelation('uasGrades', $allUasGrades->get($student->id, collect()));
            $student->setRelation('extracurriculars', $allEkskuls->get($student->id, collect()));
            $student->setRelation('uasNote', $allNotes->get($student->id));
            $student->setRelation('attendance', $allAttendances->get($student->id));
        }

        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'tanggal_raport' => 'Karawang, 22 Desember 2026',
        ]);

        return view('uas_reports.print-all', compact('students', 'school', 'academicYear', 'subjects'));
    }
}
