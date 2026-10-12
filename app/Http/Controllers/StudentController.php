<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with(['classroom', 'academicYear'])->paginate(15);

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        $classrooms = Classroom::all();
        $academicYears = AcademicYear::all();

        return view('students.create', compact('classrooms', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'required|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student): RedirectResponse
    {
        return redirect()->route('students.edit', $student);
    }

    public function edit(Student $student): View
    {
        $classrooms = Classroom::all();
        $academicYears = AcademicYear::all();

        return view('students.edit', compact('student', 'classrooms', 'academicYears'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'required|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        // Delete related grades and attendance if any
        $student->grades()->delete();
        $student->attendance()->delete();
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
