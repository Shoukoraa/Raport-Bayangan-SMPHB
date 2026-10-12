<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::orderBy('order_number')->get();

        return view('subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $nextOrder = (Subject::max('order_number') ?? 0) + 1;

        return view('subjects.create', compact('nextOrder'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'order_number' => 'required|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Subject::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('subjects.edit', $subject);
    }

    public function edit(Subject $subject): View
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'order_number' => 'required|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        // Delete related grades if any
        $subject->grades()->delete();
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
