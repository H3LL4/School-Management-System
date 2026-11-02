<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    public const STATUSES = ['Enrolled', 'Waitlisted', 'Dropped'];

    protected $fillable = ['student_id', 'course_id', 'semester_id', 'status'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'Enrolled'   => 'success',
            'Waitlisted' => 'warning',
            default      => 'secondary',
        };
    }
}
