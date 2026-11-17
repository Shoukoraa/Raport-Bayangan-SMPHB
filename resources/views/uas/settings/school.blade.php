<x-uas-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-900 border border-emerald-200 mb-1">
                    Workspace UAS
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Pengaturan Kop Surat Raport Resmi (UAS / PAS)
                </h2>
                <p class="text-sm text-slate-700 mt-0.5 font-medium">
                    Atur identitas sekolah, judul e-Rapor resmi semester, dan penandatangan raport akhir.
                </p>
            </div>
            <a href="{{ route('uas-reports.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg shadow-sm text-sm transition-colors">
                &larr; Kembali ke Raport UAS
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl shadow-xs flex items-center justify-between">
                    <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-xl shadow-xs">
                    <ul class="text-sm font-semibold text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('settings.school.update') }}" method="POST" class="bg-white overflow-hidden shadow-xs rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="workspace" value="uas">

                <!-- KOP & JUDUL -->
                <div class="border-b border-slate-200 pb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-1">
                        1. Informasi Header & Satuan Pendidikan
                    </h3>
                    <p class="text-xs text-slate-700 mb-4 font-medium">Informasi resmi yang tercetak pada kop lembar raport akhir semester.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $school->nama_sekolah) }}" required
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Alamat Sekolah</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $school->alamat) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Judul Kegiatan / Lembar Raport</label>
                            <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $school->judul_laporan) }}" required
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                            <p class="text-xs text-slate-700 mt-1">Contoh: Laporan Hasil Belajar Akhir Semester (Kurikulum Merdeka)</p>
                        </div>
                    </div>
                </div>

                <!-- TANDA TANGAN -->
                <div class="border-b border-slate-200 pb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-1">
                        2. Tanggal & Penandatangan Raport UAS
                    </h3>
                    <p class="text-xs text-slate-700 mb-4 font-medium">Nama dan NIP resmi kepala sekolah serta wali kelas yang bertanda tangan.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Tempat & Tanggal Raport Dibagikan</label>
                            <input type="text" name="tanggal_raport" value="{{ old('tanggal_raport', $school->tanggal_raport) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                            <p class="text-xs text-slate-700 mt-1">Contoh: Karawang, 22 Desember 2026</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Nama Kepala Sekolah</label>
                            <input type="text" name="nama_kepala_sekolah" value="{{ old('nama_kepala_sekolah', $school->nama_kepala_sekolah) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">NIP Kepala Sekolah</label>
                            <input type="text" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $school->nip_kepala_sekolah) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">Nama Wali Kelas</label>
                            <input type="text" name="nama_wali_kelas" value="{{ old('nama_wali_kelas', $school->nama_wali_kelas) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">NIP Wali Kelas</label>
                            <input type="text" name="nip_wali_kelas" value="{{ old('nip_wali_kelas', $school->nip_wali_kelas) }}"
                                class="w-full rounded-lg border-slate-300 text-base font-semibold focus:border-emerald-600 focus:ring-emerald-600">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('uas-reports.index') }}" class="px-5 py-3 rounded-lg border border-slate-300 text-slate-800 text-sm font-bold hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">
                        Simpan Pengaturan Kop UAS
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-uas-layout>
