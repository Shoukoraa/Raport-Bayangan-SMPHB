<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-purple-100 text-purple-900 border border-purple-200 mb-1">
                    Pusat Keamanan & Arsip
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Cadangan & Pemulihan Data (SQLite)
                </h2>
                <p class="text-sm text-slate-700 mt-1 font-medium">
                    Simpan salinan data raport ke komputer Anda atau pulihkan data saat dibutuhkan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('portal') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow-xs transition-colors">
                    <span>⌂ Kembali ke Portal Utama</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl shadow-xs flex items-center justify-between">
                    <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-600 p-4 rounded-xl shadow-xs">
                    <span class="text-sm font-bold text-rose-900">Perhatian:</span>
                    <ul class="list-disc list-inside text-xs font-semibold text-rose-800 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 2 PILIHAN UTAMA: SIMPAN CADANGAN & KEMBALIKAN DATA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 1. SIMPAN CADANGAN -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                            <div class="p-3 bg-emerald-100 text-emerald-900 rounded-xl shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Simpan Cadangan Data</h3>
                                <p class="text-xs text-slate-700 font-medium">Amankan seluruh data raport ke komputer atau laptop Anda.</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-700 leading-relaxed mb-6 font-medium">
                            Unduh seluruh data siswa, mata pelajaran, nilai raport UTS, nilai UAS, dan riwayat semester dalam satu file database aman. Simpan file ini di flashdisk atau folder dokumen Anda.
                        </p>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('database.download-current') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-base font-bold shadow-xs transition-colors text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Unduh Cadangan ke Komputer</span>
                        </a>

                        <form action="{{ route('database.create-snapshot') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold border border-slate-300 transition-colors">
                                <span>Simpan Salinan Cadangan Baru di Server</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. PULIHKAN DATA -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                            <div class="p-3 bg-blue-100 text-blue-900 rounded-xl shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Pulihkan Data dari File</h3>
                                <p class="text-xs text-slate-700 font-medium">Kembalikan data dari file cadangan yang tersimpan di komputer Anda.</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-700 leading-relaxed mb-4 font-medium">
                            Pilih file cadangan (<span class="font-mono text-blue-800 font-bold">.sqlite</span>) dari komputer. Sistem akan mengembalikan seluruh data raport persis seperti saat file cadangan tersebut dibuat.
                        </p>

                        <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-950 mb-4 flex items-center gap-2.5 font-medium">
                            <svg class="w-5 h-5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span><strong>Otomatis Aman:</strong> Sebelum data dipulihkan, sistem akan selalu membuat salinan otomatis dari data yang sedang aktif.</span>
                        </div>
                    </div>

                    <form action="{{ route('database.restore-upload') }}" method="POST" enctype="multipart/form-data"
                          onsubmit="return confirm('PERINGATAN PEMULIHAN DATA:\n\nApakah Anda yakin ingin memulihkan database dari file ini?\n\nSeluruh data raport saat ini akan diperbarui sesuai isi file cadangan. Sistem otomatis mencadangkan data aktif sebelum proses berjalan.');"
                          class="pt-4 border-t border-slate-100">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-slate-800 uppercase mb-1">Pilih File Cadangan:</label>
                            <input type="file" name="database_file" accept=".sqlite,.db,.bak" required
                                   class="block w-full text-xs text-slate-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border file:border-slate-300 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-base font-bold shadow-xs transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span>Pulihkan Data Sekarang</span>
                        </button>
                    </form>
                </div>

            </div>

            <!-- TABEL ARSIP CADANGAN DI SERVER (RAMAH BAHASA MANUSIA) -->
            @if(count($backupFiles) > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Daftar Salinan Cadangan di Server</h3>
                            <p class="text-xs text-slate-700 font-medium">Riwayat salinan pengaman yang tersimpan di sistem. Anda dapat mengunduh atau memulihkan data sewaktu-waktu.</p>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-800 rounded-lg border border-slate-300">
                            {{ count($backupFiles) }} Salinan Tersedia
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-[15px]">
                            <thead class="bg-slate-100 text-slate-900 text-xs font-extrabold uppercase tracking-wider">
                                <tr>
                                    <th class="py-4 px-4 text-left min-w-[280px]">Keterangan Salinan Cadangan</th>
                                    <th class="py-4 px-4 text-center w-28">Ukuran File</th>
                                    <th class="py-4 px-4 text-center w-48">Waktu Pembuatan</th>
                                    <th class="py-4 px-4 text-right min-w-[240px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($backupFiles as $file)
                                    @php
                                        $fname = $file['name'];
                                        if (str_contains($fname, 'pre_restore')) {
                                            $humanTitle = 'Cadangan Otomatis Sebelum Pemulihan';
                                            $badgeClass = 'bg-amber-100 text-amber-900 border-amber-300';
                                        } elseif (str_contains($fname, 'manual')) {
                                            $humanTitle = 'Cadangan Manual Dibuat Pengguna';
                                            $badgeClass = 'bg-blue-100 text-blue-900 border-blue-300';
                                        } else {
                                            $humanTitle = 'Salinan Cadangan Database';
                                            $badgeClass = 'bg-slate-100 text-slate-900 border-slate-300';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-100 transition-colors">
                                        <!-- Kolom Keterangan Ramah Pengguna -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border {{ $badgeClass }}">
                                                    {{ $humanTitle }}
                                                </span>
                                            </div>
                                            <div class="font-extrabold text-slate-900 text-[15px]">
                                                {{ $humanTitle }} &mdash; {{ $file['modified'] }}
                                            </div>
                                            <div class="text-xs font-mono text-slate-700 mt-0.5">
                                                Nama file: {{ $file['name'] }}
                                            </div>
                                        </td>

                                        <!-- Ukuran File -->
                                        <td class="py-4 px-4 whitespace-nowrap text-center text-sm text-slate-800 font-mono font-bold">
                                            {{ $file['size'] }}
                                        </td>

                                        <!-- Waktu Disimpan -->
                                        <td class="py-4 px-4 whitespace-nowrap text-center text-sm font-semibold text-slate-700">
                                            {{ $file['modified'] }}
                                        </td>

                                        <!-- Kolom Aksi dengan Jarak Aman -->
                                        <td class="py-4 px-4 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center gap-2.5">
                                                <!-- Unduh -->
                                                <a href="{{ route('database.download-snapshot', $file['name']) }}"
                                                   class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-900 rounded-lg text-xs font-bold border border-slate-300 transition-colors"
                                                   title="Unduh file cadangan ini ke komputer">
                                                    📥 Unduh
                                                </a>

                                                <!-- Pulihkan -->
                                                <form action="{{ route('database.restore-snapshot') }}" method="POST" class="inline"
                                                      onsubmit="return confirm('KONFIRMASI PEMULIHAN:\n\nApakah Anda yakin ingin mengembalikan seluruh data raport ke tanggal:\n{{ $file['modified'] }}?\n\nSistem akan otomatis mencadangkan data aktif saat ini sebelum memulihkan.');">
                                                    @csrf
                                                    <input type="hidden" name="filename" value="{{ $file['name'] }}">
                                                    <button type="submit" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-2xs transition-colors">
                                                        ↺ Pulihkan Data Ini
                                                    </button>
                                                </form>

                                                <!-- Pemisah / Jarak Aman -->
                                                <div class="h-6 w-px bg-slate-300 mx-1"></div>

                                                <!-- Hapus (Tombol Berbahaya Terpisah) -->
                                                <form action="{{ route('database.delete-snapshot', $file['name']) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('PERINGATAN HAPUS:\n\nApakah Anda yakin ingin menghapus salinan cadangan tanggal {{ $file['modified'] }} ini secara permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-2 text-rose-700 hover:text-rose-900 hover:bg-rose-50 rounded-lg text-xs font-bold transition-colors">
                                                        🗑 Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
