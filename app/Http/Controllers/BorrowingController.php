<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Services\ActivityLogger;
use App\Services\LibraryNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BorrowingController extends Controller
{
    /** Create a pending request after checking availability and duplicate active loans. */
    public function store(Book $book, ActivityLogger $log)
    {
        if ($book->available_copies < 1) {
            return back()->withErrors(['book' => 'No copies available.']);
        }

        // Group the OR so only pending or unreturned approved requests block duplicates.
        $exists = Borrowing::where('user_id', auth()->id())->where('book_id', $book->id)
            ->where(function ($q) {
                $q->where('status', 'pending')->orWhere(fn ($q) => $q->where('status', 'approved')->where('is_returned', false));
            })->exists();
        if ($exists) {
            return back()->withErrors(['book' => 'You already have an active request or borrowing for this book.']);
        }

        Borrowing::create(['user_id' => auth()->id(), 'book_id' => $book->id, 'status' => 'pending', 'requested_at' => now()]);
        $log->write('BORROW_REQUESTED', $book->title);

        return back()->with('success', 'Request submitted for librarian review.');
    }

    /** Show the signed-in student's paginated borrowing history. */
    public function mine()
    {
        $requests = Borrowing::with('book')->where('user_id', auth()->id())->latest()->paginate(10);

        return view('mine', compact('requests'));
    }

    /** Show all borrowing requests to the librarian. */
    public function index()
    {
        $requests = Borrowing::with(['user', 'book'])->latest()->paginate(15);

        return view('requests', compact('requests'));
    }

    /** Approve or reject a request while locking inventory updates in a transaction. */
    public function decision(Request $request, Borrowing $borrowing, ActivityLogger $log, LibraryNotifier $notify)
    {
        $data = $request->validate(['decision' => 'required|in:approved,rejected', 'librarian_note' => 'nullable|string|max:500']);
        try {
            DB::transaction(function () use ($borrowing, $data) {
                $b = Borrowing::lockForUpdate()->findOrFail($borrowing->id);
                if ($b->status !== 'pending') {
                    throw new \RuntimeException('Request already processed.');
                }

                $dueAt = null;
                if ($data['decision'] === 'approved') {
                    $book = Book::lockForUpdate()->findOrFail($b->book_id);
                    if ($book->available_copies < 1) {
                        throw new \RuntimeException('No copies available.');
                    }

                    $book->decrement('available_copies');
                    $dueAt = now()->addDays(3);
                }
                $b->update(['status' => $data['decision'], 'decided_at' => now(), 'due_at' => $dueAt, 'librarian_note' => $data['librarian_note'] ?? null]);
            });
            $borrowing->refresh()->load(['user', 'book']);
            $log->write('BORROW_'.strtoupper($data['decision']), $borrowing->book->title);
            $notify->decision($borrowing);

            return back()->with('success', 'Request '.$data['decision'].' successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['decision' => 'Unable to process request: '.$e->getMessage()]);
        }
    }

    /** Let only the borrowing student request a return confirmation. */
    public function requestReturn(Borrowing $borrowing, ActivityLogger $log)
    {
        abort_unless($borrowing->user_id === auth()->id(), 403);
        if ($borrowing->status !== 'approved' || $borrowing->is_returned) {
            return back()->withErrors(['return' => 'This borrowing cannot be returned.']);
        }

        if ($borrowing->return_requested_at) {
            return back()->with('success', 'Your return request is already awaiting librarian confirmation.');
        }

        $borrowing->update(['return_requested_at' => now()]);
        $log->write('RETURN_REQUESTED', $borrowing->book->title);

        return back()->with('success', 'Return request submitted. Please hand the physical book to the librarian for confirmation.');
    }

    /** Confirm a physical return and restore the available copy atomically. */
    public function confirmReturn(Borrowing $borrowing, ActivityLogger $log)
    {
        try {
            DB::transaction(function () use ($borrowing) {
                $b = Borrowing::lockForUpdate()->findOrFail($borrowing->id);
                if ($b->status !== 'approved' || $b->is_returned) {
                    throw new \RuntimeException('This borrowing is not active.');
                }

                if (! $b->return_requested_at) {
                    throw new \RuntimeException('The student has not submitted a return request.');
                }

                $book = Book::lockForUpdate()->findOrFail($b->book_id);
                $book->increment('available_copies');
                $b->update(['is_returned' => true, 'returned_at' => now()]);
            });
            $borrowing->refresh()->load('book');
            $log->write('BOOK_RETURN_CONFIRMED', $borrowing->book->title);

            return back()->with('success', 'Book return confirmed. Available copies restored.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['return' => 'Unable to confirm return: '.$e->getMessage()]);
        }
    }

    /** Send a reminder for an active, unreturned borrowing. */
    public function remind(Borrowing $borrowing, LibraryNotifier $notify, ActivityLogger $log)
    {
        if ($borrowing->status !== 'approved' || $borrowing->is_returned) {
            return back()->withErrors(['reminder' => 'A reminder can only be sent for an active borrowing.']);
        }

        try {
            $notify->returnReminder($borrowing);
            $log->write('RETURN_REMINDER_SENT', $borrowing->book->title);

            return back()->with('success', 'Return reminder sent using configured email and SMS services.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['reminder' => 'Unable to send reminder: '.$e->getMessage()]);
        }
    }
}
