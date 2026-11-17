<x-uts-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-blue-100 text-blue-900 border border-blue-200 mb-1">
                    Workspace UTS &bull; Halaman Kerja Terpadu
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Kelola & Cetak Nilai UTS (Raport Bayangan)
                </h2>
                <p class="text-sm text-slate-700 mt-1 font-medium">
                    Satu halaman untuk mengisi nilai siswa, melihat lembar raport, unduh file Excel, atau cetak 1 kelas sekaligus.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('reports.print-all', ['academic_year_id' => $selectedYear?->id, 'status' => $statusFilter]) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>🖨 Cetak Semua Raport UTS (1 Kelas)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- FILTER & PENCARIAN SISWA (SENIOR FRIENDLY) -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-2xs">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    
                    <!-- PILIH PERIODE TAHUN PELAJARAN / SEMESTER -->
                    <div class="lg:col-span-4">
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">
                            Periode Raport (T.A & Semester)
                        </label>
                        <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-base rounded-xl border-slate-300 font-semibold text-slate-900 focus:border-blue-600 focus:ring-blue-600 py-2.5">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear && $selectedYear->id == $year->id ? 'selected' : '' }}>
                                    {{ $year->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PILIH STATUS SISWA -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">
                            Status Siswa
                        </label>
                        <select name="status" onchange="this.form.submit()" class="w-full text-base rounded-xl border-slate-300 font-semibold text-slate-900 focus:border-blue-600 focus:ring-blue-600 py-2.5">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Siswa</option>
                            <option value="Aktif" {{ $statusFilter == 'Aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                            <option value="Lulus" {{ $statusFilter == 'Lulus' ? 'selected' : '' }}>Alumni / Lulus (Arsip)</option>
                            <option value="Pindah" {{ $statusFilter == 'Pindah' ? 'selected' : '' }}>Pindah Sekolah</option>
                            <option value="Keluar" {{ $statusFilter == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                    </div>

                    <!-- CARI SISWA -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">
                            Cari Siswa
                        </label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik Nama / NISN / NIS..."
                               class="w-full text-base rounded-xl border-slate-300 font-semibold text-slate-900 focus:border-blue-600 focus:ring-blue-600 py-2.5 placeholder:text-slate-700">
                    </div>

                    <!-- TOMBOL CARI -->
                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-sm font-bold shadow-xs transition-colors">
                            Cari Siswa
                        </button>
                        @if($search || $statusFilter !== 'all' || ($selectedYear && $selectedYear->id != $activeYear?->id))
                            <a href="{{ route('reports.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-bold border border-slate-300 transition-colors text-center" title="Hapus Pencarian">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Status Aktif Bar -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between text-sm text-slate-700 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900">Periode Terpilih:</span>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-md font-bold bg-blue-50 text-blue-900 border border-blue-200">
                            {{ $selectedYear ? $selectedYear->full_name : 'Semua Periode' }}
                        </span>
                        @if($selectedYear && !$selectedYear->is_active)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded border border-amber-300">
                                ⚠ Melihat arsip periode lampau
                            </span>
                        @endif
                    </div>
                    <div>
                        Menampilkan <span class="font-extrabold text-slate-900">{{ $students->total() }}</span> siswa
                    </div>
                </div>
            </div>

            <!-- TABEL KERJA TERPADU SISWA & NILAI UTS (SENIOR FRIENDLY) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-[15px]">
                        <thead class="bg-slate-100 text-slate-900 text-xs font-extrabold uppercase tracking-wider">
                            <tr>
                                <th class="py-4 px-4 text-center w-14">No</th>
                                <th class="py-4 px-4 text-left min-w-[200px]">Nama Lengkap Siswa</th>
                                <th class="py-4 px-4 text-left min-w-[140px]">NISN / NIS</th>
                                <th class="py-4 px-4 text-center w-28">Kelas</th>
                                <th class="py-4 px-4 text-center w-36">Status Siswa</th>
                                <th class="py-4 px-4 text-center w-36">Progres Nilai</th>
                                <th class="py-4 px-4 text-center min-w-[320px]">Aksi Pengelolaan Raport</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-100 transition-colors">
                                <!-- No -->
                                <td class="py-4 px-4 whitespace-nowrap text-center text-slate-900 font-bold">
                                    {{ $students->firstItem() + $index }}
                                </td>

                                <!-- Nama Siswa -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-extrabold text-slate-900 text-[15px]">{{ $student->name }}</div>
                                    <div class="text-xs text-slate-700 font-medium capitalize mt-0.5">{{ $student->gender ?? '-' }}</div>
                                </td>

                                <!-- NISN / NIS -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-800 font-mono text-sm font-bold">
                                    {{ $student->nisn ? $student->nisn : '-' }} / {{ $student->nis ?? '-' }}
                                </td>

                                <!-- Kelas -->
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-900 border border-blue-300">
                                        Kelas {{ $student->classroom->name ?? 'VII' }}
                                    </span>
                                </td>

                                <!-- Status Siswa -->
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    @if($student->status == 'Lulus')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-900 border border-purple-300">
                                            Alumni (Lulus)
                                        </span>
                                    @elseif($student->status == 'Pindah')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            Pindah
                                        </span>
                                    @elseif($student->status == 'Keluar')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-300">
                                            Keluar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            Siswa Aktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Progres Nilai UTS -->
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    @if(($student->current_grades_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-300">
                                            <svg class="w-4 h-4 text-emerald-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            {{ $student->current_grades_count }} Mapel Terisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Pengelolaan Raport (Lega, Berlabel Jelas & Tanpa Ikon Bisu) -->
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Tombol Utama: Isi / Edit Nilai -->
                                        <a href="{{ route('grades.edit', ['student' => $student->id, 'academic_year_id' => $selectedYear?->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow-2xs transition-colors">
                                            <span>✎ Isi / Edit Nilai</span>
                                        </a>

                                        <!-- Tombol Lihat Raport -->
                                        <a href="{{ route('reports.preview', ['student' => $student->id, 'academic_year_id' => $selectedYear?->id]) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-900 rounded-lg text-sm font-bold border-2 border-slate-300 shadow-2xs transition-colors">
                                            <span>👁 Lihat Raport</span>
                                        </a>

                                        <!-- Tombol Unduh Excel -->
                                        <a href="{{ route('reports.excel', ['student' => $student->id, 'academic_year_id' => $selectedYear?->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-bold shadow-2xs transition-colors"
                                           title="Unduh Lembar Raport Format Excel">
                                            <span>📥 Unduh Excel</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center text-slate-700">
                                    <div class="flex flex-col items-center justify-center">
                                        <p class="text-base font-bold text-slate-900">Tidak ada data siswa ditemukan.</p>
                                        <p class="text-sm text-slate-700 mt-1 font-medium">Periksa kata kunci pencarian atau ganti pilihan status siswa.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                    <div class="p-5 border-t border-slate-200 bg-slate-50">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-uts-layout>
