<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class YearLevel extends Model
{
    protected $fillable = [
        'year_level_name'
    ];
    public function student_records(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
