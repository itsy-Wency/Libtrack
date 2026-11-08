<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    protected $fillable = ['user_id', 'book_id', 'status', 'requested_at', 'decided_at', 'due_at', 'return_requested_at', 'returned_at', 'is_returned', 'librarian_note'];

    /** Cast stored dates and return state to types used by the application. */
    protected function casts(): array
    {
        return [
            'requested_at'        => 'datetime',
            'decided_at'          => 'datetime',
            'due_at'              => 'datetime',
            'return_requested_at' => 'datetime',
            'returned_at'         => 'datetime',
            'is_returned'         => 'boolean'];
    }

    /** Return the student who made this borrowing request. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Return the book associated with this borrowing request. */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /** Report whether an approved, unreturned borrowing is past its due date. */
    public function isOverdue(): bool
    {
        return $this->status === 'approved' && ! $this->is_returned && $this->due_at !== null && $this->due_at->isPast();
    }
}
