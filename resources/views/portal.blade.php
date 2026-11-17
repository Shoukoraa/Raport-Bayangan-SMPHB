<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Portal Sistem Raport - SMP Harapan Bangsa</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900 min-h-screen flex flex-col justify-between">
        
        <!-- Top Bar Ringkas & Tenang -->
        <header class="bg-white border-b border-slate-200 py-4 px-6 sm:px-10">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMP Harapan Bangsa" class="h-11 w-11 object-contain">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 leading-tight">SMP HARAPAN BANGSA</h1>
                        <p class="text-xs font-semibold text-slate-700">Portal Utama Pengelolaan Raport</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-block text-sm font-semibold text-slate-700">
                        {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-red-700 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Body: 2 Kartu Aksi Utama & Tombol Sekunder -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1 flex flex-col justify-center">
            
            <!-- Judul Sambutan Singkat -->
            <div class="text-center mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    Pilih Layanan Raport
                </h2>
                <p class="text-sm sm:text-base text-slate-700 mt-2 font-medium">
                    Silakan pilih sistem raport yang ingin Anda kelola hari ini.
                </p>
            </div>

            <!-- DUA KARTU AKSI UTAMA BERDAMPINGAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-4xl mx-auto w-full">
                
                <!-- Kartu 1: UTS (Tema Biru Solid) -->
                <div class="bg-white rounded-2xl border-2 border-blue-600 shadow-md p-6 sm:p-8 flex flex-col justify-between text-left">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-blue-100 text-blue-900 mb-4 border border-blue-300">
                            Modul 1
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                            SISTEM RAPORT BAYANGAN (UTS)
                        </h3>
                        <p class="text-sm sm:text-base text-slate-700 font-medium mt-3 leading-relaxed">
                            Pengelolaan nilai tugas, ulangan harian, dan pencetakan lembar raport bayangan tengah semester.
                        </p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('uts.dashboard') }}" 
                           class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-blue-700 hover:bg-blue-800 text-white text-base font-bold rounded-xl shadow-sm transition-colors text-center">
                            Masuk ke Raport UTS &rarr;
                        </a>
                    </div>
                </div>

                <!-- Kartu 2: UAS (Tema Hijau Emerald Solid) -->
                <div class="bg-white rounded-2xl border-2 border-emerald-600 shadow-md p-6 sm:p-8 flex flex-col justify-between text-left">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-emerald-100 text-emerald-900 mb-4 border border-emerald-300">
                            Modul 2
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                            SISTEM RAPORT RESMI (UAS / PAS)
                        </h3>
                        <p class="text-sm sm:text-base text-slate-700 font-medium mt-3 leading-relaxed">
                            Pengelolaan nilai akhir semester, capaian kompetensi, projek P5, dan cetak e-Rapor resmi pemerintah.
                        </p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('uas.dashboard') }}" 
                           class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white text-base font-bold rounded-xl shadow-sm transition-colors text-center">
                            Masuk ke Raport UAS &rarr;
                        </a>
                    </div>
                </div>

            </div>

            <!-- GARIS PEMISAH HALUS -->
            <div class="max-w-4xl mx-auto w-full my-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-300"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase tracking-wider font-bold">
                        <span class="bg-slate-100 px-4 text-slate-700">Data Master Bersama</span>
                    </div>
                </div>
            </div>

            <!-- 3 TOMBOL SEKUNDER MASTER DATA BERUKURAN SEDANG -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-4xl mx-auto w-full">
                <a href="{{ route('students.index') }}" 
                   class="flex items-center justify-center gap-2.5 px-4 py-3 bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-950 font-bold text-sm rounded-xl border border-slate-300 shadow-2xs transition-colors">
                    <svg class="w-5 h-5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Data Siswa</span>
                </a>

                <a href="{{ route('subjects.index') }}" 
                   class="flex items-center justify-center gap-2.5 px-4 py-3 bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-950 font-bold text-sm rounded-xl border border-slate-300 shadow-2xs transition-colors">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Mata Pelajaran</span>
                </a>

                <a href="{{ route('settings.database') }}" 
                   class="flex items-center justify-center gap-2.5 px-4 py-3 bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-950 font-bold text-sm rounded-xl border border-slate-300 shadow-2xs transition-colors">
                    <svg class="w-5 h-5 text-purple-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Cadangan Database (SQLite)</span>
                </a>
            </div>

        </main>

        <!-- Footer Ringkas -->
        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-700 font-medium">
            SMP Harapan Bangsa Karawang &copy; {{ date('Y') }} &mdash; Sistem Informasi Akademik Raport
        </footer>

    </body>
</html>
