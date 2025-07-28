<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'year_published',
        'course_id',
        'img_book',
        'quantity',
    ];

    protected $casts = [
        'img_book' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }
}
