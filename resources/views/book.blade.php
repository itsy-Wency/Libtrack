{{-- Book details view: show availability and the student borrowing action. --}}
@extends('layouts.app')

@section('title', $book->title . ' | LibTrack')

@section('content')
    <div class="page-heading">
        <a class="btn secondary" href="{{ route('catalog') }}">&larr; Back to catalog</a>
    </div>

    <article class="card detail-layout">
        <div>
            <p class="eyebrow">{{ $book->category?->name ?? 'Library collection' }}</p>
            <h1>{{ $book->title }}</h1>
            <p class="muted">by {{ $book->author }}</p>
            <div class="detail-list">
                <div class="detail-item"><strong>ISBN</strong>{{ $book->isbn ?? 'Not provided' }}</div>
                <div class="detail-item">
                    <strong>Availability</strong>
                    <span class="availability"><span class="availability-dot {{ $book->available_copies < 1 ? 'is-empty' : '' }}"></span>
                        {{ $book->available_copies }} of {{ $book->total_copies }} available</span>
                </div>
            </div>
            @if ($book->description)
                <h2>About this book</h2>
                <p>{{ $book->description }}</p>
            @endif
        </div>
        <aside class="card" aria-label="Borrow this book">
            <p class="eyebrow">Ready when you are</p>
            <h2>Make it your next read</h2>
            @if (auth()->user()->role === 'student' && $book->available_copies > 0)
                <p class="muted">Send a request and the librarian will confirm availability.</p>
                <form method="POST" action="{{ route('borrow', $book) }}">
                    @csrf
                    <button type="submit"><svg class="icon"><use href="#icon-book" /></svg>Request to borrow</button>
                </form>
            @elseif ($book->available_copies < 1)
                <p class="muted">This title is currently checked out. Check back soon.</p>
            @else
                <p class="muted">Sign in as a student to request this book.</p>
            @endif
        </aside>
    </article>
@endsection
