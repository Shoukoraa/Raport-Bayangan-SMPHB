<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Dashboard Raport Bayangan') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">SMP Harapan Bangsa Karawang</p>
            </div>
            <a href="{{ route('academic-years.index') }}" title="Klik untuk mengelola tahun pelajaran & semester" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300 hover:bg-blue-200 transition-all">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>T.A. {{ $activeYear->name ?? '2026/2027' }} - Semester {{ $activeYear->semester ?? 1 }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Statistik Ringkas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <a href="{{ route('students.index') }}" class="block bg-white overflow-hidden shadow-xs hover:shadow-md rounded-xl border border-gray-200 p-6 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Siswa</div>
                            <div class="text-3xl font-extrabold text-blue-800 mt-1">{{ $totalStudents }}</div>
                            <div class="text-xs text-gray-500 mt-1">Kelas Aktif & Riwayat</div>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-700 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Card 2 -->
                <a href="{{ route('subjects.index') }}" class="block bg-white overflow-hidden shadow-xs hover:shadow-md rounded-xl border border-gray-200 p-6 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Mata Pelajaran</div>
                            <div class="text-3xl font-extrabold text-emerald-800 mt-1">{{ $totalSubjects }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $totalSubjects }} Mapel Aktif</div>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Card 3 -->
                <a href="{{ route('academic-years.index') }}" class="block bg-white overflow-hidden shadow-xs hover:shadow-md rounded-xl border border-gray-200 p-6 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Tahun Pelajaran</div>
                            <div class="text-2xl font-extrabold text-indigo-800 mt-1">{{ $activeYear->name ?? '2026/2027' }}</div>
                            <div class="text-xs text-gray-500 mt-1">Semester {{ $activeYear->semester ?? 1 }} ({{ $activeYear ? $activeYear->semester_name : 'Ganjil' }})</div>
                        </div>
                        <div class="p-3 bg-indigo-50 text-indigo-700 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Modul 1: Raport Bayangan (UTS) -->
            <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-blue-200/80 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                                Modul 1 • Raport Bayangan
                            </span>
                            <h3 class="text-base font-extrabold text-gray-900">Tengah Semester (UTS)</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Pengelolaan nilai format SMP Harapan Bangsa (Tugas, UH, Tertulis & Praktek).</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-800 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                        Fokus Utama Sekolah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3.5 p-4 bg-blue-700 hover:bg-blue-800 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Daftar Raport UTS</div>
                            <div class="text-xs text-blue-100 font-normal mt-0.5">Lihat hasil & unduh Excel per siswa</div>
                        </div>
                    </a>

                    <a href="{{ route('grades.index') }}" class="flex items-center gap-3.5 p-4 bg-indigo-700 hover:bg-indigo-800 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Input Nilai UTS</div>
                            <div class="text-xs text-indigo-100 font-normal mt-0.5">Perbarui PR, UH, Tertulis & Praktek</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.print-all') }}" target="_blank" class="flex items-center gap-3.5 p-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Cetak Semua Raport UTS</div>
                            <div class="text-xs text-emerald-100 font-normal mt-0.5">Cetak 1 kelas langsung (A4 page break)</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modul 2: Raport Akhir Semester (UAS / Pemerintah) -->
            <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-teal-200/80 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-200">
                                Modul 2 • Raport Akhir Semester
                            </span>
                            <h3 class="text-base font-extrabold text-gray-900">UAS (Kurikulum Merdeka)</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Format resmi Kemendikbudristek (Nilai Akhir, Capaian Kompetensi, Kokurikuler P5, Ekstrakurikuler).</p>
                    </div>
                    <span class="text-xs font-semibold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                        Template Resmi Pemerintah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('uas-reports.index') }}" class="flex items-center gap-3.5 p-4 bg-teal-700 hover:bg-teal-800 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Daftar & Input Raport UAS</div>
                            <div class="text-xs text-teal-100 font-normal mt-0.5">Kelola NA, Capaian TP, P5 & cetak 3 halaman e-Rapor</div>
                        </div>
                    </a>

                    <a href="{{ route('uas-reports.print-all') }}" target="_blank" class="flex items-center gap-3.5 p-4 bg-cyan-700 hover:bg-cyan-800 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Cetak Semua Raport UAS</div>
                            <div class="text-xs text-cyan-100 font-normal mt-0.5">Cetak 1 kelas sekaligus format pemerintah</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modul 3: Pengaturan & Keamanan -->
            <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-gray-200 p-6">
                <h3 class="text-base font-extrabold text-gray-900 mb-1">Pengaturan & Keamanan Data</h3>
                <p class="text-xs text-gray-500 mb-4">Pengelolaan master data, riwayat semester, dan cadangan database.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('academic-years.index') }}" class="flex items-center gap-3.5 p-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Riwayat Tahun Pelajaran</div>
                            <div class="text-xs text-slate-300 font-normal mt-0.5">Kelola semester & arsip masa lalu</div>
                        </div>
                    </a>

                    <a href="{{ route('settings.database') }}" class="flex items-center gap-3.5 p-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Cadangan & Pemulihan Data</div>
                            <div class="text-xs text-slate-300 font-normal mt-0.5">Backup / snapshot database SQLite</div>
                        </div>
                    </a>

                    <a href="{{ route('settings.school') }}" class="flex items-center gap-3.5 p-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl shadow-xs transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold">Pengaturan Kop Surat</div>
                            <div class="text-xs text-slate-300 font-normal mt-0.5">Kepsek, wali kelas & info sekolah</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
