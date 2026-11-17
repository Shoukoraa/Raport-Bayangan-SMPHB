<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    public function uasGrades(): HasMany
    {
        return $this->hasMany(StudentUasGrade::class);
    }

    public function extracurriculars(): HasMany
    {
        return $this->hasMany(StudentExtracurricular::class)->orderBy('order_number');
    }

    public function uasNotes(): HasMany
    {
        return $this->hasMany(StudentUasNote::class);
    }

    public function uasNoteForYear(?int $academicYearId = null): ?StudentUasNote
    {
        if (! $academicYearId) {
            return $this->uasNotes()->first();
        }

        return $this->uasNotes()->where('academic_year_id', $academicYearId)->first();
    }

    /**
     * Get attendance for a specific academic year.
     */
    public function attendanceForYear(?int $academicYearId = null): ?Attendance
    {
        if (! $academicYearId) {
            return $this->attendance;
        }

        return $this->attendances()->where('academic_year_id', $academicYearId)->first();
    }
}
