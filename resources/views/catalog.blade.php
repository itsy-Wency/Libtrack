{{-- Catalog view: filter the collection and show each book's availability. --}}
@extends('layouts.app')

@section('title', 'Library Catalog | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Find your next read</p>
            <h1>Library catalog</h1>
            <p class="muted">Explore the collection, discover a topic, or find a familiar favorite.</p>
        </div>
    </div>

    <form class="card catalog-tools" method="GET" action="{{ route('catalog') }}">
        <div class="grid">
            <div>
                <label for="catalog-search">Search the collection</label>
                <input id="catalog-search" name="q" value="{{ request('q') }}" placeholder="Try a title or author">
            </div>
            <div>
                <label for="catalog-category">Browse by category</label>
                <select id="catalog-category" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="submit"><svg class="icon"><use href="#icon-search" /></svg>Search books</button>
            </div>
        </div>
    </form>

    @if (auth()->user()->role === 'student')
        <section class="card recommendation-card" aria-labelledby="recommendation-title">
            <span class="recommendation-mark"><svg class="icon"><use href="#icon-sparkle" /></svg></span>
            <div>
                <h2 id="recommendation-title">A little help choosing?</h2>
                <p class="muted">Share an interest and get a suggestion from the library collection.</p>
                <form class="button-row" method="POST" action="{{ route('ai') }}">
                    @csrf
                    <input name="interest" aria-label="Your interests" placeholder="For example, web development" required>
                    <button type="submit"><svg class="icon"><use href="#icon-sparkle" /></svg>Suggest a book</button>
                </form>
            </div>
        </section>
    @endif

    <div class="page-heading">
        <div>
            <p class="eyebrow">The collection</p>
            <h2>Books to explore</h2>
        </div>
        <p class="muted">{{ $books->total() }} {{ $books->total() === 1 ? 'book' : 'books' }}</p>
    </div>

    <div class="grid catalog-grid">
        @forelse ($books as $book)
            <article class="card book-card">
                @if ($book->cover_path)
                    <img class="cover" src="{{ asset('storage/' . $book->cover_path) }}" alt="Cover of {{ $book->title }}">
                @else
                    <div class="cover" aria-hidden="true"></div>
                @endif
                <p class="eyebrow">{{ $book->category?->name ?? 'Uncategorized' }}</p>
                <h3>{{ $book->title }}</h3>
                <p class="muted">{{ $book->author }}</p>
                <span class="availability">
                    <span class="availability-dot {{ $book->available_copies < 1 ? 'is-empty' : '' }}"></span>
                    {{ $book->available_copies > 0 ? $book->available_copies . ' available' : 'Currently unavailable' }}
                </span>
                <div class="card-actions">
                    <a class="btn secondary" href="{{ route('books.show', $book) }}">
                        View details <svg class="icon"><use href="#icon-arrow" /></svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="card empty-state">
                <svg class="icon"><use href="#icon-search" /></svg>
                <h3>No books found</h3>
                <p>Try another title, author, or category.</p>
                <a class="btn secondary" href="{{ route('catalog') }}">Clear search</a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">{{ $books->links() }}</div>
@endsection
