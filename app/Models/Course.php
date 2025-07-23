<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{

    protected $fillable = [
        'course_name'
    ];
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function student_records(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
