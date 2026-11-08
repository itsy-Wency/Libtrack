{{-- Librarian collection view: summarize books and provide edit/delete actions. --}}
@extends('layouts.app')

@section('title', 'Manage Books | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Librarian workspace</p>
            <h1>Manage books</h1>
            <p class="muted">Keep the collection details and available copies up to date.</p>
        </div>
        <a class="btn" href="{{ route('lib.books.create') }}"><svg class="icon"><use href="#icon-plus" /></svg>Add a book</a>
    </div>

    <section class="card table-wrap" aria-label="Books in the collection">
        <table>
            <thead><tr><th>Book</th><th>Category</th><th>Copies</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td><strong>{{ $book->title }}</strong><br><span class="muted">{{ $book->author }}</span></td>
                        <td>{{ $book->category?->name }}</td>
                        <td>{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                        <td>
                            <div class="button-row">
                                <a class="btn secondary" href="{{ route('lib.books.edit', $book) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('lib.books.destroy', $book) }}" onsubmit="return confirm('Delete this book? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No books in the collection yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{ $books->links() }}
@endsection
