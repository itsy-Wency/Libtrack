{{-- Shared create/edit form for book details, inventory, and an optional cover. --}}
@extends('layouts.app')

@section('title', (isset($book) ? 'Edit book' : 'Add a book') . ' | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Librarian workspace</p>
            <h1>{{ isset($book) ? 'Edit book' : 'Add a book' }}</h1>
            <p class="muted">Enter clear details so readers can find the right title.</p>
        </div>
        <a class="btn secondary" href="{{ route('lib.books.index') }}">&larr; Back to books</a>
    </div>

    <form class="card" method="POST" enctype="multipart/form-data" action="{{ isset($book) ? route('lib.books.update', $book) : route('lib.books.store') }}">
        @csrf
        @if (isset($book))
            @method('PUT')
        @endif

        <div class="form-grid">
            <div>
                <label for="title">Title</label>
                <input id="title" name="title" value="{{ old('title', $book->title ?? '') }}" required autocomplete="off">
            </div>
            <div>
                <label for="author">Author</label>
                <input id="author" name="author" value="{{ old('author', $book->author ?? '') }}" required autocomplete="off">
            </div>
            <div>
                <label for="isbn">ISBN <span class="muted">(optional)</span></label>
                <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" autocomplete="off">
            </div>
            <div>
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $book->category_id ?? '') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="total_copies">Total copies</label>
                <input id="total_copies" type="number" name="total_copies" min="1" value="{{ old('total_copies', $book->total_copies ?? 1) }}" required>
            </div>
            <div>
                <label for="available_copies">Available copies</label>
                <input id="available_copies" type="number" name="available_copies" min="0" value="{{ old('available_copies', $book->available_copies ?? 1) }}" required>
            </div>
        </div>

        <label for="description">Description <span class="muted">(optional)</span></label>
        <textarea id="description" name="description" placeholder="A short description for readers">{{ old('description', $book->description ?? '') }}</textarea>

        <label for="cover">Cover image <span class="muted">(optional, up to 2 MB)</span></label>
        <input id="cover" type="file" name="cover" accept="image/*">

        <div class="form-actions">
            <button type="submit"><svg class="icon"><use href="#icon-check" /></svg>{{ isset($book) ? 'Save changes' : 'Add book' }}</button>
            <a class="btn secondary" href="{{ route('lib.books.index') }}">Cancel</a>
        </div>
    </form>
@endsection
