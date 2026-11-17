<x-uts-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-blue-100 text-blue-900 border border-blue-200 mb-1">
                    Workspace UTS &bull; Form Nilai Siswa
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Isi & Perbarui Nilai UTS: {{ $student->name }}
                </h2>
                <p class="text-sm text-slate-700 mt-1 font-medium">
                    Kelas: <span class="font-black text-slate-900">{{ $student->classroom->name ?? 'VII' }}</span> | NISN: <span class="font-mono font-bold text-slate-900">{{ $student->nisn ?? '-' }}</span> | NIS: <span class="font-mono font-bold text-slate-900">{{ $student->nis ?? '-' }}</span>
                </p>
            </div>
            <!-- Switcher Periode untuk Siswa Ini -->
            <form method="GET" action="{{ route('grades.edit', $student->id) }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 uppercase">Periode:</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="text-sm rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 font-bold bg-white shadow-2xs py-2">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $academicYear && $academicYear->id == $year->id ? 'selected' : '' }}>
                            {{ $year->full_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl shadow-xs flex items-center justify-between">
                    <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-600 p-4 rounded-xl shadow-xs">
                    <span class="text-sm font-bold text-rose-900">Periksa kembali data yang dimasukkan:</span>
                    <ul class="list-disc list-inside text-xs font-semibold text-rose-800 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 sm:p-8">
                
                <form action="{{ route('grades.store', $student->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                    
                    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-blue-50 p-4 rounded-xl border border-blue-200 gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-900 uppercase">Periode Nilai:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-black bg-blue-700 text-white">
                                {{ $academicYear->name }} - Semester {{ $academicYear->semester }} ({{ $academicYear->semester_name }})
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Skala nilai rentang 0 - 100</span>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-slate-200 border border-slate-300 text-[15px]">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th rowspan="2" class="py-3 px-3 border border-slate-300 text-center text-xs font-black text-slate-900 uppercase tracking-wider w-12">No</th>
                                    <th rowspan="2" class="py-3 px-4 border border-slate-300 text-left text-xs font-black text-slate-900 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th colspan="2" class="py-2.5 px-3 border border-slate-300 text-center text-xs font-black text-blue-900 uppercase bg-blue-100/70">Formatif Harian</th>
                                    <th colspan="2" class="py-2.5 px-3 border border-slate-300 text-center text-xs font-black text-emerald-900 uppercase bg-emerald-100/70">Sumatif Tengah Semester</th>
                                </tr>
                                <tr>
                                    <th class="py-2 px-3 border border-slate-300 text-center text-xs font-bold text-blue-900 uppercase bg-blue-50">PR / Tugas</th>
                                    <th class="py-2 px-3 border border-slate-300 text-center text-xs font-bold text-blue-900 uppercase bg-blue-50">Ulangan Harian</th>
                                    <th class="py-2 px-3 border border-slate-300 text-center text-xs font-bold text-emerald-900 uppercase bg-emerald-50">Ujian Tertulis</th>
                                    <th class="py-2 px-3 border border-slate-300 text-center text-xs font-bold text-emerald-900 uppercase bg-emerald-50">Praktik</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($subjects as $index => $subject)
                                    @php
                                        $grade = $grades->get($subject->id);
                                    @endphp
                                    <tr class="hover:bg-slate-100 transition-colors">
                                        <td class="py-3 px-3 border border-slate-300 text-center text-sm font-bold text-slate-700">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4 border border-slate-300 font-extrabold text-slate-900">
                                            {{ $subject->name }}
                                            @if(!$subject->is_active)
                                                <span class="text-xs text-slate-700 font-normal italic">(Non-aktif)</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-2 border border-slate-300 bg-blue-50/30">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][pr]" value="{{ old('grades.'.$subject->id.'.pr', $grade->pr ?? '') }}" placeholder="-" class="w-full text-base border-2 border-slate-300 rounded-lg shadow-2xs focus:border-blue-600 focus:ring-blue-600 text-center font-extrabold text-slate-900 py-1.5">
                                        </td>
                                        <td class="py-2 px-2 border border-slate-300 bg-blue-50/30">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][uh]" value="{{ old('grades.'.$subject->id.'.uh', $grade->uh ?? '') }}" placeholder="-" class="w-full text-base border-2 border-slate-300 rounded-lg shadow-2xs focus:border-blue-600 focus:ring-blue-600 text-center font-extrabold text-slate-900 py-1.5">
                                        </td>
                                        <td class="py-2 px-2 border border-slate-300 bg-emerald-50/30">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][tertulis]" value="{{ old('grades.'.$subject->id.'.tertulis', $grade->tertulis ?? '') }}" placeholder="-" class="w-full text-base border-2 border-slate-300 rounded-lg shadow-2xs focus:border-blue-600 focus:ring-blue-600 text-center font-extrabold text-slate-900 py-1.5">
                                        </td>
                                        <td class="py-2 px-2 border border-slate-300 bg-emerald-50/30">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][praktek]" value="{{ old('grades.'.$subject->id.'.praktek', $grade->praktek ?? '') }}" placeholder="-" class="w-full text-base border-2 border-slate-300 rounded-lg shadow-2xs focus:border-blue-600 focus:ring-blue-600 text-center font-extrabold text-slate-900 py-1.5">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Presensi & Catatan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                            <h3 class="font-extrabold text-base text-slate-900 mb-3 flex items-center gap-2">
                                Rekap Kehadiran (Jumlah Hari)
                            </h3>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase mb-1">Sakit (S)</label>
                                    <input type="number" min="0" name="attendance[sakit]" value="{{ old('attendance.sakit', $attendance->sakit ?? 0) }}" class="w-full text-base rounded-xl border-2 border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-center font-bold py-2">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase mb-1">Izin (I)</label>
                                    <input type="number" min="0" name="attendance[izin]" value="{{ old('attendance.izin', $attendance->izin ?? 0) }}" class="w-full text-base rounded-xl border-2 border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-center font-bold py-2">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase mb-1">Alpa (A)</label>
                                    <input type="number" min="0" name="attendance[alpa]" value="{{ old('attendance.alpa', $attendance->alpa ?? 0) }}" class="w-full text-base rounded-xl border-2 border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-center font-bold py-2">
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                            <h3 class="font-extrabold text-base text-slate-900 mb-3 flex items-center gap-2">
                                Catatan Wali Kelas
                            </h3>
                            <div>
                                <textarea name="attendance[note]" rows="3" class="w-full text-base rounded-xl border-2 border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-slate-900 font-medium" placeholder="Catatan motivasi atau evaluasi untuk siswa...">{{ old('attendance.note', $attendance->note ?? 'Tetap semangat belajar ya nak!') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Simpan & Kembali -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 pt-6">
                        <a href="{{ route('reports.index', ['academic_year_id' => $academicYear->id]) }}" class="w-full sm:w-auto text-center px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold rounded-xl text-sm border border-slate-300 transition-colors">
                            &larr; Kembali ke Kelola Nilai UTS
                        </a>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="submit" name="action" value="save" class="px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl shadow-xs text-sm transition-colors">
                                💾 Simpan Nilai UTS
                            </button>
                            <button type="submit" name="action" value="preview" class="px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl shadow-xs text-sm transition-colors">
                                Simpan & Lihat Raport &rarr;
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-uts-layout>
