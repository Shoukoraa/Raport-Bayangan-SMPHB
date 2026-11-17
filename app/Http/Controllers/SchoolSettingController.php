<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolSettingController extends Controller
{
    private function getOrCreateSetting(): SchoolSetting
    {
        return SchoolSetting::firstOrCreate([], [
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => null,
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
    }

    public function edit(): View
    {
        $school = $this->getOrCreateSetting();

        return view('settings.school', compact('school'));
    }

    public function editUts(): View
    {
        $school = $this->getOrCreateSetting();

        return view('uts.settings.school', compact('school'));
    }

    public function editUas(): View
    {
        $school = $this->getOrCreateSetting();

        return view('uas.settings.school', compact('school'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'judul_laporan' => 'required|string|max:255',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:100',
            'nama_wali_kelas' => 'nullable|string|max:255',
            'nip_wali_kelas' => 'nullable|string|max:100',
            'tanggal_raport' => 'nullable|string|max:100',
        ]);

        $school = SchoolSetting::first();
        if ($school) {
            $school->update($validated);
        } else {
            SchoolSetting::create($validated);
        }

        $workspace = $request->input('workspace');
        if ($workspace === 'uts') {
            return redirect()->route('uts.settings.school')->with('success', 'Pengaturan Kop Surat UTS berhasil disimpan.');
        } elseif ($workspace === 'uas') {
            return redirect()->route('uas.settings.school')->with('success', 'Pengaturan Kop Surat UAS berhasil disimpan.');
        }

        return redirect()->route('settings.school')->with('success', 'Pengaturan Kop Surat, Nama Kegiatan, dan Tanda Tangan berhasil disimpan.');
    }
}
