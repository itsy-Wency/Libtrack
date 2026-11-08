{{-- Shared create/edit form for the name and description of a category. --}}
@extends('layouts.app')

@section('title', (isset($category) ? 'Edit category' : 'Add a category') . ' | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Librarian workspace</p>
            <h1>{{ isset($category) ? 'Edit category' : 'Add a category' }}</h1>
            <p class="muted">Use a short, familiar name readers can recognize.</p>
        </div>
        <a class="btn secondary" href="{{ route('lib.categories.index') }}">&larr; Back to categories</a>
    </div>

    <form class="card" method="POST" action="{{ isset($category) ? route('lib.categories.update', $category) : route('lib.categories.store') }}">
        @csrf
        @if (isset($category))
            @method('PUT')
        @endif

        <label for="name">Category name</label>
        <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required maxlength="100" autocomplete="off">

        <label for="description">Description <span class="muted">(optional)</span></label>
        <textarea id="description" name="description" placeholder="What kinds of books belong here?">{{ old('description', $category->description ?? '') }}</textarea>

        <div class="form-actions">
            <button type="submit"><svg class="icon"><use href="#icon-check" /></svg>{{ isset($category) ? 'Save changes' : 'Add category' }}</button>
            <a class="btn secondary" href="{{ route('lib.categories.index') }}">Cancel</a>
        </div>
    </form>
@endsection
