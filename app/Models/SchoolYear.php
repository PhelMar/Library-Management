<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{

    protected $fillable = [
        'school_year_name'
    ];

    public function student_records(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
