<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'gender', 'subject'];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
