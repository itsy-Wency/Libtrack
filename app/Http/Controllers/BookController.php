<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /** List books with categories for the librarian's collection view. */
    public function index()
    {
        $books = Book::with('category')->latest()->paginate(12);

        return view('books.index', compact('books'));
    }

    /** Show the form for adding a book. */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('books.form', compact('categories'));
    }

    /** Validate book details, store an optional cover, and record the new book. */
    public function store(Request $r, ActivityLogger $log)
    {
        $d = $this->valid($r);
        if ($r->hasFile('cover')) {
            $d['cover_path'] = $r->file('cover')->store('covers', 'public');
        }

        unset($d['cover']);
        $b = Book::create($d);
        $log->write('BOOK_CREATED', $b->title);

        return redirect()->route('lib.books.index')->with('success', 'Book added.');
    }

    /** Show the edit form with the book and available categories. */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        return view('books.form', compact('book', 'categories'));
    }

    /** Save validated book changes and replace the cover when a new one is uploaded. */
    public function update(Request $r, Book $book, ActivityLogger $log)
    {
        $d = $this->valid($r, $book);
        if ($r->hasFile('cover')) {
            $d['cover_path'] = $r->file('cover')->store('covers', 'public');
        }
        unset($d['cover']);
        $book->update($d);
        $log->write('BOOK_UPDATED', $book->title);

        return redirect()->route('lib.books.index')->with('success', 'Book updated.');
    }

    /** Block deletion while requests are active, then remove the cover and book. */
    public function destroy(Book $book, ActivityLogger $log)
    {
        if (Borrowing::where('book_id', $book->id)->whereIn('status', ['pending', 'approved'])->exists()) {
            return back()->withErrors(['book' => 'Cannot delete a book with active requests.']);
        }

        $title = $book->title;
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();
        $log->write('BOOK_DELETED', $title);

        return back()->with('success', 'Book deleted.');
    }

    /** Return only valid book fields and apply an ISBN uniqueness exception when editing. */
    private function valid(Request $r, ?Book $b = null): array
    {
        return $r->validate([
            'category_id'  => 'required|exists:categories,id',
            'title'        => 'required|string|max:255', 'author'                                              => 'required|string|max:255',
            'isbn'         => 'nullable|string|max:40|unique:books,isbn,' . ($b?->id ?? 'NULL'), 'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1', 'available_copies'                                     => 'required|integer|min:0|lte:total_copies', 'cover' => 'nullable|image|max:2048',
        ]);
    }
}
