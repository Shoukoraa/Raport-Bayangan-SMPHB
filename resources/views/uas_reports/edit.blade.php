<x-uas-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-900 border border-emerald-200 mb-1">
                    Workspace UAS &bull; Form Nilai Akhir & Catatan
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Isi & Perbarui Raport UAS: {{ $student->name }}
                </h2>
                <p class="text-sm text-slate-700 mt-1 font-medium">
                    Kelas: <span class="font-black text-slate-900">{{ $student->classroom->name ?? 'VII' }}</span> | NISN: <span class="font-mono font-bold text-slate-900">{{ $student->nisn ?? '-' }}</span> | NIS: <span class="font-mono font-bold text-slate-900">{{ $student->nis ?? '-' }}</span>
                </p>
            </div>

            <!-- Periode Switcher -->
            <form method="GET" action="{{ route('uas-reports.edit', $student->id) }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 uppercase">Periode:</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 font-bold bg-white shadow-2xs py-2">
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
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm font-bold text-emerald-900">{{ session('success') }}</span>
                    </div>
                    <a href="{{ route('uas-reports.preview', ['student' => $student->id, 'academic_year_id' => $academicYear?->id]) }}" target="_blank" class="text-sm font-bold text-emerald-900 hover:underline">
                        Lihat Cetak Raport UAS &rarr;
                    </a>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-600 p-4 rounded-xl shadow-xs">
                    <span class="text-sm font-bold text-rose-900">Periksa kembali isian formulir:</span>
                    <ul class="list-disc list-inside text-xs font-semibold text-rose-800 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('uas-reports.store', $student->id) }}" method="POST" id="form-uas-report">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $academicYear?->id }}">

                <!-- BAGIAN 1: NILAI AKHIR & CAPAIAN KOMPETENSI MATA PELAJARAN -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden mb-6">
                    <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                1. Nilai Akhir (NA) & Capaian Kompetensi Mata Pelajaran
                            </h3>
                            <p class="text-xs text-slate-700 mt-0.5 font-medium">
                                Format Kurikulum Merdeka Fase {{ $uasNote->fase ?? 'D' }}. Tuliskan capaian yang sudah sangat baik serta aspek yang perlu ditingkatkan.
                            </p>
                        </div>
                        <button type="button" onclick="autoFillFromUts()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 text-emerald-900 rounded-xl text-xs font-bold transition">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Salin Nilai dari UTS</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[15px]">
                            <thead>
                                <tr class="bg-slate-100 text-slate-900 font-extrabold uppercase text-xs border-b border-slate-200">
                                    <th class="py-3.5 px-3 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4 min-w-[200px]">Mata Pelajaran</th>
                                    <th class="py-3.5 px-3 text-center w-28 bg-slate-50">Acuan Awal</th>
                                    <th class="py-3.5 px-3 text-center w-32 bg-emerald-100/70 text-emerald-950">Nilai Akhir (NA)</th>
                                    <th class="py-3.5 px-4 min-w-[280px]">Capaian Kompetensi (Sangat Baik)</th>
                                    <th class="py-3.5 px-4 min-w-[280px]">Capaian Perlu Peningkatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($subjects as $index => $subject)
                                    @php
                                        $uasGrade = $uasGrades->get($subject->id);
                                        $utsGrade = $utsGrades->get($subject->id);
                                        $utsAvg = $utsGrade ? $utsGrade->average : null;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3.5 px-3 text-center text-slate-700 font-bold">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900">
                                            {{ $subject->name }}
                                        </td>
                                        <td class="py-3.5 px-3 text-center font-mono text-slate-700 bg-slate-50 font-bold">
                                            <span class="midterm-ref-val" data-subject-id="{{ $subject->id }}">
                                                {{ $utsAvg !== null ? number_format($utsAvg, 0) : '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 bg-emerald-50/40 text-center">
                                            <input type="number" step="0.1" min="0" max="100"
                                                   name="grades[{{ $subject->id }}][nilai_akhir]"
                                                   id="na-input-{{ $subject->id }}"
                                                   value="{{ old("grades.{$subject->id}.nilai_akhir", $uasGrade?->nilai_akhir) }}"
                                                   placeholder="0-100"
                                                   class="w-24 text-center font-black text-slate-900 rounded-xl border-2 border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2 text-base shadow-2xs">
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <textarea name="grades[{{ $subject->id }}][capaian_tertinggi]"
                                                      rows="2"
                                                      placeholder="Contoh: Menunjukkan penguasaan yang sangat baik dalam memahami konsep..."
                                                      class="w-full text-sm font-medium rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 leading-relaxed">{{ old("grades.{$subject->id}.capaian_tertinggi", $uasGrade?->capaian_tertinggi) }}</textarea>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <textarea name="grades[{{ $subject->id }}][capaian_perlu_peningkatan]"
                                                      rows="2"
                                                      placeholder="Contoh: Perlu bimbingan dan peningkatan dalam mempraktikkan..."
                                                      class="w-full text-sm font-medium rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 leading-relaxed">{{ old("grades.{$subject->id}.capaian_perlu_peningkatan", $uasGrade?->capaian_perlu_peningkatan) }}</textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-700 font-semibold">
                                            Belum ada mata pelajaran aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BAGIAN 2: KOKURIKULER P5 & EKSTRAKURIKULER -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Projek Penguatan Profil Pelajar Pancasila (P5) -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-bold">2</span>
                            <h3 class="text-base font-bold text-slate-900">Projek Penguatan Profil Pelajar Pancasila (P5)</h3>
                        </div>
                        <p class="text-xs text-slate-700 mb-3 font-medium">Deskripsi perkembangan karakter dan partisipasi projek murid selama semester ini.</p>
                        <textarea name="note[kokurikuler_deskripsi]" rows="5"
                                  class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-3 leading-relaxed"
                                  placeholder="Contoh: Pada semester ini, ananda aktif dan menunjukkan kemandirian serta gotong royong...">{{ old('note.kokurikuler_deskripsi', $uasNote->kokurikuler_deskripsi) }}</textarea>
                    </div>

                    <!-- Ekstrakurikuler -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-bold">3</span>
                            <h3 class="text-base font-bold text-slate-900">Ekstrakurikuler (Maks. 3 Kegiatan)</h3>
                        </div>
                        <p class="text-xs text-slate-700 mb-3 font-medium">Kegiatan bakat dan minat yang diikuti murid beserta predikat capaiannya.</p>

                        <div class="space-y-3">
                            @for($i = 0; $i < 3; $i++)
                                @php
                                    $ekskul = $extracurriculars->get($i);
                                @endphp
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mb-2">
                                        <div class="sm:col-span-5">
                                            <input type="text" name="extracurriculars[{{ $i }}][kegiatan]"
                                                   value="{{ old("extracurriculars.{$i}.kegiatan", $ekskul?->kegiatan) }}"
                                                   placeholder="Nama Kegiatan (cth: Pramuka)"
                                                   class="w-full text-xs rounded-lg border-slate-300 font-semibold focus:border-emerald-600 focus:ring-emerald-600 py-1.5">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <select name="extracurriculars[{{ $i }}][predikat]"
                                                    class="w-full text-xs rounded-lg border-slate-300 font-bold focus:border-emerald-600 focus:ring-emerald-600 py-1.5">
                                                <option value="" {{ old("extracurriculars.{$i}.predikat", $ekskul?->predikat) == '' ? 'selected' : '' }}>- Predikat -</option>
                                                <option value="Sangat Baik" {{ old("extracurriculars.{$i}.predikat", $ekskul?->predikat) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                                <option value="Baik" {{ old("extracurriculars.{$i}.predikat", $ekskul?->predikat) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Cukup" {{ old("extracurriculars.{$i}.predikat", $ekskul?->predikat) == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                <option value="Kurang" {{ old("extracurriculars.{$i}.predikat", $ekskul?->predikat) == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="text" name="extracurriculars[{{ $i }}][keterangan]"
                                                   value="{{ old("extracurriculars.{$i}.keterangan", $ekskul?->keterangan) }}"
                                                   placeholder="Keterangan singkat"
                                                   class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-1.5">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 3: KEHADIRAN & CATATAN WALI KELAS -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-bold">4</span>
                        <h3 class="text-base font-bold text-slate-900">Rekap Kehadiran, Catatan Wali Kelas & Titimangsa</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Presensi -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <h4 class="text-xs font-bold uppercase text-slate-800 mb-3">Ketidakhadiran (Hari)</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-xs font-semibold text-slate-700">Sakit (S)</label>
                                    <input type="number" min="0" name="attendance[sakit]"
                                           value="{{ old('attendance.sakit', $attendance?->sakit ?? 0) }}"
                                           class="w-20 text-center text-sm font-bold rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-1">
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-xs font-semibold text-slate-700">Izin (I)</label>
                                    <input type="number" min="0" name="attendance[izin]"
                                           value="{{ old('attendance.izin', $attendance?->izin ?? 0) }}"
                                           class="w-20 text-center text-sm font-bold rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-1">
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-xs font-semibold text-slate-700">Tanpa Keterangan (A)</label>
                                    <input type="number" min="0" name="attendance[alpa]"
                                           value="{{ old('attendance.alpa', $attendance?->alpa ?? 0) }}"
                                           class="w-20 text-center text-sm font-bold rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-1">
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Wali Kelas & Titimangsa -->
                        <div class="md:col-span-2 space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 uppercase mb-1">
                                    Catatan Wali Kelas
                                </label>
                                <textarea name="note[catatan_wali_kelas]" rows="3"
                                          class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 leading-relaxed"
                                          placeholder="Motivasi, apresiasi, atau saran untuk kemajuan belajar murid...">{{ old('note.catatan_wali_kelas', $uasNote->catatan_wali_kelas) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-800 uppercase mb-1">
                                    Tempat & Tanggal Rapor (Titimangsa)
                                </label>
                                <input type="text" name="note[tanggal_raport]"
                                       value="{{ old('note.tanggal_raport', $uasNote->tanggal_raport ?? 'Karawang, 22 Desember 2026') }}"
                                       class="w-full sm:w-80 text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 font-bold py-2"
                                       placeholder="Karawang, 22 Desember 2026">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN & PREVIEW -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('uas-reports.index', ['academic_year_id' => $academicYear?->id]) }}"
                       class="text-sm font-bold text-slate-800 hover:text-slate-950 px-4 py-2.5 rounded-lg border border-slate-300 bg-white">
                        &larr; Kembali ke Kelola Raport UAS
                    </a>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="submit" name="action" value="save"
                                class="flex-1 sm:flex-none px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold shadow-xs transition">
                            💾 Simpan Nilai UAS
                        </button>
                        <button type="submit" name="action" value="preview"
                                class="flex-1 sm:flex-none px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow-xs transition inline-flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Simpan & Lihat Raport &rarr;</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function autoFillFromUts() {
            const refEls = document.querySelectorAll('.midterm-ref-val');
            let filled = 0;
            refEls.forEach(el => {
                const subId = el.getAttribute('data-subject-id');
                const val = el.textContent.trim();
                const targetInput = document.getElementById('na-input-' + subId);
                if (targetInput && val && val !== '-' && (!targetInput.value || targetInput.value === '0')) {
                    targetInput.value = val;
                    filled++;
                }
            });
            if (filled > 0) {
                alert('Berhasil mengisi ' + filled + ' Nilai Akhir (NA) berdasarkan acuan rata-rata tengah semester.');
            } else {
                alert('Semua Nilai Akhir (NA) sudah terisi atau nilai acuan belum tersedia.');
            }
        }
    </script>
</x-uas-layout>
