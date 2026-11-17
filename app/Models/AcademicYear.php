<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'semester' => 'integer',
        'is_active' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getSemesterNameAttribute(): string
    {
        return $this->semester == 1 ? 'Ganjil' : 'Genap';
    }

    public function getFullNameAttribute(): string
    {
        $status = $this->is_active ? ' [Aktif]' : '';

        return "{$this->name} - Semester {$this->semester} ({$this->semester_name}){$status}";
    }
}
