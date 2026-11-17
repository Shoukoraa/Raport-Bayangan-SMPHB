<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                        UTS • Input Nilai
                    </span>
                    <span class="text-xs text-gray-400">SMP Harapan Bangsa</span>
                </div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Input Nilai Raport Bayangan (UTS)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola dan perbarui nilai harian (PR, UH) serta sumatif (Tertulis, Praktek) untuk raport tengah semester.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.index', ['academic_year_id' => $selectedYear?->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-2xs transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Lihat Daftar Raport UTS</span>
                </a>
                <a href="{{ route('academic-years.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-xs font-bold shadow-2xs transition-all">
                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Kelola Periode</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- FILTER BAR -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs">
                <form method="GET" action="{{ route('grades.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                    
                    <div class="lg:col-span-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                            Periode Pengisian Nilai (T.A & Semester)
                        </label>
                        <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600 font-medium">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear && $selectedYear->id == $year->id ? 'selected' : '' }}>
                                    {{ $year->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                            Pencarian Siswa
                        </label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN / NIS..."
                               class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                    </div>

                    <div class="lg:col-span-3 flex items-center gap-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-3.5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-2xs transition-all">
                            Filter
                        </button>
                        @if($search || ($selectedYear && $selectedYear->id != $activeYear?->id))
                            <a href="{{ route('grades.index') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-all text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-700">Target Periode:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md font-bold bg-blue-50 text-blue-800 border border-blue-200">
                            {{ $selectedYear ? $selectedYear->full_name : 'Semua Periode' }}
                        </span>
                    </div>
                    <div>
                        Menampilkan <span class="font-bold text-gray-900">{{ $students->total() }}</span> siswa
                    </div>
                </div>
            </div>

            <!-- TABEL SISWA & NILAI -->
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5 text-center w-12">No</th>
                                <th class="px-5 py-3.5 text-left">Nama Siswa</th>
                                <th class="px-5 py-3.5 text-left">NISN / NIS</th>
                                <th class="px-5 py-3.5 text-center">Kelas</th>
                                <th class="px-5 py-3.5 text-center">Progres Nilai</th>
                                <th class="px-5 py-3.5 text-center">Aksi Pengisian Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap text-center text-gray-600 font-semibold">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap font-bold text-gray-900">
                                    {{ $student->name }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-gray-700 font-mono text-xs font-medium">
                                    {{ $student->nisn ? $student->nisn : '-' }} / {{ $student->nis ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                                        Kelas {{ $student->classroom->name ?? 'VII' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if(($student->current_grades_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            {{ $student->current_grades_count }} Mapel Terisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('grades.edit', ['student' => $student->id, 'academic_year_id' => $selectedYear?->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-2xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Input / Edit Nilai
                                        </a>
                                        <a href="{{ route('reports.preview', ['student' => $student->id, 'academic_year_id' => $selectedYear?->id]) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow-2xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat Raport
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Belum ada data siswa ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
