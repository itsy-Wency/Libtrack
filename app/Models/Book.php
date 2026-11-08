<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = ['category_id', 'title', 'author', 'isbn', 'description', 'total_copies', 'available_copies', 'cover_path'];

    /** Return the category assigned to this book. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
