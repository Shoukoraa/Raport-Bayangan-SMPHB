<x-uts-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-blue-100 text-blue-900 border border-blue-200 mb-1">
                    Workspace UTS
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Dashboard Raport Bayangan (UTS)
                </h2>
                <p class="text-sm text-slate-700 mt-1 font-medium">
                    SMP Harapan Bangsa Karawang &mdash; Pengelolaan Nilai Tengah Semester
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-white text-blue-900 border-2 border-blue-200 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <span>T.A. {{ $activeYear->name ?? '2026/2027' }} - Semester {{ $activeYear->semester ?? 1 }} ({{ $activeYear?->semester_name ?? 'Ganjil' }})</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Ringkasan Statistik 3 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Siswa -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-2xs">
                    <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Siswa Terdaftar</div>
                    <div class="text-3xl font-black text-blue-900 mt-2">{{ $totalStudents }}</div>
                    <div class="text-xs text-slate-700 mt-1 font-medium">Data siswa aktif dalam sistem</div>
                </div>

                <!-- Mata Pelajaran Aktif -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-2xs">
                    <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Mata Pelajaran Aktif</div>
                    <div class="text-3xl font-black text-slate-900 mt-2">{{ $totalSubjects }}</div>
                    <div class="text-xs text-slate-700 mt-1 font-medium">Mapel yang dinilai pada raport bayangan</div>
                </div>

                <!-- Status Pengisian Nilai UTS -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-2xs">
                    <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Siswa Sudah Terisi Nilai</div>
                    <div class="text-3xl font-black text-blue-700 mt-2">{{ $gradedStudentsCount }} <span class="text-base font-bold text-slate-700">/ {{ $totalStudents }} Siswa</span></div>
                    <div class="text-xs text-slate-700 mt-1 font-medium">Pada periode semester aktif saat ini</div>
                </div>
            </div>

            <!-- DUA KARTU KERJA UTAMA UTS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Kartu Aksi 1: Kelola Nilai & Raport UTS -->
                <div class="bg-white rounded-2xl border-2 border-blue-600 p-7 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-blue-100 text-blue-900 mb-3 border border-blue-300">
                            Halaman Kerja Terpadu
                        </div>
                        <h3 class="text-xl font-black text-slate-900">
                            Kelola & Cetak Nilai UTS
                        </h3>
                        <p class="text-sm text-slate-700 font-medium mt-2 leading-relaxed">
                            Buka daftar siswa untuk mengisi nilai harian (PR, UH), nilai sumatif (Tertulis, Praktek), melihat lembar raport per siswa, dan mengunduh Excel.
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('reports.index') }}" 
                           class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-blue-700 hover:bg-blue-800 text-white text-base font-bold rounded-xl shadow-xs transition-colors text-center">
                            ✎ Buka Halaman Kerja Nilai UTS &rarr;
                        </a>
                    </div>
                </div>

                <!-- Kartu Aksi 2: Cetak Semua Raport UTS -->
                <div class="bg-white rounded-2xl border-2 border-slate-300 p-7 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-100 text-emerald-900 mb-3 border border-emerald-300">
                            Pencetakan Masal
                        </div>
                        <h3 class="text-xl font-black text-slate-900">
                            Cetak Semua Raport UTS
                        </h3>
                        <p class="text-sm text-slate-700 font-medium mt-2 leading-relaxed">
                            Cetak lembar raport bayangan seluruh siswa sekaligus dalam satu kelas aktif (siap cetak langsung ke kertas A4 dengan pemisah halaman rapi).
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('reports.print-all') }}" target="_blank"
                           class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white text-base font-bold rounded-xl shadow-xs transition-colors text-center">
                            🖨 Cetak Semua Raport UTS (1 Kelas) &rarr;
                        </a>
                    </div>
                </div>

            </div>

            <!-- PENGATURAN KOP & JALAN PINTAS -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-2xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Pengaturan Kop Surat & Tanda Tangan UTS</h4>
                    <p class="text-xs text-slate-700 mt-0.5 font-medium">Ubah nama kepala sekolah, NIP, nama wali kelas, atau tanggal pembagian raport bayangan.</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('uts.settings.school') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition-colors">
                        ⚙ Atur Kop Raport UTS
                    </a>
                    <a href="{{ route('portal') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg border border-slate-300 transition-colors">
                        ⌂ Portal Utama
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-uts-layout>
