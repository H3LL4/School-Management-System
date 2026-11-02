<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'gender',
        'dob',
        'enrollment_date',
        'description',
        'classroom_id',
    ];

    protected function casts(): array
    {
        return [
            'dob'             => 'date',
            'enrollment_date' => 'date',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
