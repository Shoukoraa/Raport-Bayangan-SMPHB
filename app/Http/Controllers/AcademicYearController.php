<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::withCount(['studentGrades', 'students', 'attendances'])
            ->orderByDesc('name')
            ->orderByDesc('semester')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true);

        return view('academic_years.index', compact('academicYears', 'activeYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
            'semester' => ['required', 'integer', 'in:1,2'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.regex' => 'Format tahun ajaran harus berupa format Tahun/Tahun (contoh: 2026/2027).',
        ]);

        $exists = AcademicYear::where('name', $validated['name'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Tahun Pelajaran dan Semester tersebut sudah pernah didaftarkan.',
            ]);
        }

        $isActive = $request->boolean('is_active') || AcademicYear::count() === 0;

        if ($isActive) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create([
            'name' => $validated['name'],
            'semester' => $validated['semester'],
            'is_active' => $isActive,
        ]);

        return redirect()->route('academic-years.index')->with('success', "Tahun Pelajaran {$academicYear->full_name} berhasil ditambahkan.");
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
            'semester' => ['required', 'integer', 'in:1,2'],
        ], [
            'name.regex' => 'Format tahun ajaran harus berupa format Tahun/Tahun (contoh: 2026/2027).',
        ]);

        $exists = AcademicYear::where('name', $validated['name'])
            ->where('semester', $validated['semester'])
            ->where('id', '!=', $academicYear->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Tahun Pelajaran dan Semester tersebut sudah pernah didaftarkan.',
            ]);
        }

        $academicYear->update([
            'name' => $validated['name'],
            'semester' => $validated['semester'],
        ]);

        return redirect()->route('academic-years.index')->with('success', 'Tahun Pelajaran berhasil diperbarui.');
    }

    public function setActive(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return redirect()->route('academic-years.index')->with('success', "Tahun Pelajaran aktif berhasil diubah ke: {$academicYear->full_name}");
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return back()->withErrors([
                'error' => 'Tahun Pelajaran yang sedang aktif tidak dapat dihapus. Silakan aktifkan tahun pelajaran lain terlebih dahulu.',
            ]);
        }

        $gradeCount = $academicYear->studentGrades()->count();
        if ($gradeCount > 0) {
            return back()->withErrors([
                'error' => "Tahun Pelajaran ini tidak dapat dihapus karena menyimpan {$gradeCount} data nilai siswa. Untuk mengamankan riwayat raport, data ini harus tetap ada.",
            ]);
        }

        $name = $academicYear->full_name;
        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', "Tahun Pelajaran {$name} berhasil dihapus.");
    }
}
