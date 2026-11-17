<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AcademicYear $year1;

    protected AcademicYear $year2;

    protected Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->year1 = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 1,
            'is_active' => false,
        ]);

        $this->year2 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create(['name' => 'VII-A']);
    }

    public function test_can_view_academic_years_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('academic-years.index'));
        $response->assertStatus(200);
        $response->assertSee('2025/2026');
        $response->assertSee('2026/2027');
    }

    public function test_can_create_new_academic_year(): void
    {
        $response = $this->actingAs($this->user)->post(route('academic-years.store'), [
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('academic-years.index'));
        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => 1,
        ]);

        // year2 should no longer be active
        $this->assertFalse($this->year2->fresh()->is_active);
    }

    public function test_can_switch_active_academic_year(): void
    {
        $response = $this->actingAs($this->user)->post(route('academic-years.set-active', $this->year1->id));
        $response->assertRedirect(route('academic-years.index'));

        $this->assertTrue($this->year1->fresh()->is_active);
        $this->assertFalse($this->year2->fresh()->is_active);
    }

    public function test_cannot_delete_active_academic_year(): void
    {
        $response = $this->actingAs($this->user)->delete(route('academic-years.destroy', $this->year2->id));
        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('academic_years', ['id' => $this->year2->id]);
    }

    public function test_cannot_delete_academic_year_with_recorded_grades(): void
    {
        $student = Student::create([
            'name' => 'Siswa Tes',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year1->id,
        ]);

        $subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'order_number' => 1,
            'is_active' => true,
        ]);

        StudentGrade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year1->id,
            'pr' => 85,
        ]);

        $response = $this->actingAs($this->user)->delete(route('academic-years.destroy', $this->year1->id));
        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('academic_years', ['id' => $this->year1->id]);
    }

    public function test_reports_filter_by_academic_year_and_graduated_student(): void
    {
        $activeStudent = Student::create([
            'name' => 'Budi Siswa Aktif',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year2->id,
            'status' => 'Aktif',
        ]);

        $alumni = Student::create([
            'name' => 'Citra Alumni Lulus',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year1->id,
            'status' => 'Lulus',
        ]);

        // Default view shows all or active
        $response = $this->actingAs($this->user)->get(route('reports.index', [
            'academic_year_id' => $this->year1->id,
            'status' => 'Lulus',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Citra Alumni Lulus');
        $response->assertDontSee('Budi Siswa Aktif');
    }
}
