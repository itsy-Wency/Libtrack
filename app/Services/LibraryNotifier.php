<?php
namespace App\Services;

use App\Models\Borrowing;
use Illuminate\Support\Facades\Mail;

class LibraryNotifier
{
    /** Notify the student after a librarian approves or rejects a request. */
    public function decision(Borrowing $borrowing): void
    {
        $borrowing->loadMissing(['user', 'book']);
        $body = "Hello {$borrowing->user->name}, your request for {$borrowing->book->title} was {$borrowing->status}.";
        Mail::raw($body, function ($message) use ($borrowing) {
            $message->to($borrowing->user->email)->subject('Library request ' . $borrowing->status);
        });
        app(MockSmsService::class)->send('DEMO-NUMBER', $body);
    }

    /** Send the student a due-date reminder through the configured demo channels. */
    public function returnReminder(Borrowing $borrowing): void
    {
        $borrowing->loadMissing(['user', 'book']);
        $due  = $borrowing->due_at?->format('F d, Y') ?? 'the assigned due date';
        $body = "Reminder: Please return '{$borrowing->book->title}' on or before {$due}. The borrowing period is three days from approval.";
        Mail::raw($body, function ($message) use ($borrowing) {
            $message->to($borrowing->user->email)->subject('Library book return reminder');
        });
        app(MockSmsService::class)->send('DEMO-NUMBER', $body);
    }
}
