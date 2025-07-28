<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrow extends Model
{
    protected $fillable = [
        'student_record_id',
        'book_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student_record(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }
}
