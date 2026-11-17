<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DualWorkspacePortalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $classroom = Classroom::create(['name' => 'VII-A']);
        Student::create([
            'classroom_id' => $classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Budi Santoso',
            'nis' => '12345',
            'nisn' => '0012345678',
            'status' => 'Aktif',
        ]);

        Subject::create([
            'code' => 'PAI',
            'name' => 'Pendidikan Agama Islam',
            'order_number' => 1,
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_portal_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('portal'));

        $response->assertOk();
        $response->assertSee('SISTEM RAPORT BAYANGAN (UTS)');
        $response->assertSee('SISTEM RAPORT RESMI (UAS / PAS)');
        $response->assertSee('Data Siswa');
        $response->assertSee('Mata Pelajaran');
        $response->assertSee('Cadangan Database (SQLite)');
    }

    public function test_dashboard_renders_portal_for_authenticated_users(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('SISTEM RAPORT BAYANGAN (UTS)');
        $response->assertSee('SISTEM RAPORT RESMI (UAS / PAS)');
    }

    public function test_user_can_view_uts_workspace_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('uts.dashboard'));

        $response->assertOk();
        $response->assertSee('Sistem Raport Bayangan (UTS)');
        $response->assertSee('Kelola & Cetak Nilai UTS', false);
        $response->assertSee('Pengaturan Kop UTS');
        $response->assertSee('Pindah ke UAS');
        $response->assertSee('Portal Utama');
    }

    public function test_user_can_view_uas_workspace_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('uas.dashboard'));

        $response->assertOk();
        $response->assertSee('Sistem Raport Resmi (UAS)');
        $response->assertSee('Kelola & Cetak Raport UAS', false);
        $response->assertSee('Pengaturan Kop UAS');
        $response->assertSee('Pindah ke UTS');
        $response->assertSee('Portal Utama');
    }

    public function test_user_can_view_unified_uts_reports_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Kelola & Cetak Nilai UTS', false);
        $response->assertSee('Isi / Edit Nilai');
        $response->assertSee('Lihat Raport');
        $response->assertSee('Unduh Excel');
        $response->assertSee('Budi Santoso');
    }

    public function test_user_can_view_uts_and_uas_kop_settings(): void
    {
        $utsResponse = $this->actingAs($this->user)->get(route('uts.settings.school'));
        $utsResponse->assertOk();
        $utsResponse->assertSee('Pengaturan Kop Surat Raport Bayangan (UTS)');

        $uasResponse = $this->actingAs($this->user)->get(route('uas.settings.school'));
        $uasResponse->assertOk();
        $uasResponse->assertSee('Pengaturan Kop Surat Raport Resmi (UAS / PAS)');
    }
}
