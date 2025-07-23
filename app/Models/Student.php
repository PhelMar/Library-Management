<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'id_no',
        'first_name',
        'middle_name',
        'last_name',
        'contact_no',
        'address'
    ];

    public function student_records(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
