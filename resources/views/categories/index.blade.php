{{-- Librarian category list with book totals and maintenance actions. --}}
@extends('layouts.app')

@section('title', 'Manage Categories | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Librarian workspace</p>
            <h1>Book categories</h1>
            <p class="muted">Group titles into useful topics for easier browsing.</p>
        </div>
        <a class="btn" href="{{ route('lib.categories.create') }}"><svg class="icon"><use href="#icon-plus" /></svg>Add a category</a>
    </div>

    <section class="card table-wrap" aria-label="Book categories">
        <table>
            <thead><tr><th>Category</th><th>Books</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong>@if ($category->description)<br><span class="muted">{{ $category->description }}</span>@endif</td>
                        <td>{{ $category->books_count }}</td>
                        <td>
                            <div class="button-row">
                                <a class="btn secondary" href="{{ route('lib.categories.edit', $category) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('lib.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No categories yet. Add one to organize the collection.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{ $categories->links() }}
@endsection
