{{-- Student view: track request status, due dates, and return confirmation. --}}
@extends('layouts.app')

@section('title', 'My Borrowing Requests | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Your reading list</p>
            <h1>My borrowing requests</h1>
            <p class="muted">Track each request and return your books on time.</p>
        </div>
        <span class="detail-item"><strong>Borrowing period</strong>3 calendar days from approval</span>
    </div>

    <section class="card table-wrap" aria-label="Your borrowing requests">
        <table>
            <thead><tr><th>Book</th><th>Status</th><th>Due date</th><th>Return</th></tr></thead>
            <tbody>
                @forelse ($requests as $item)
                    <tr>
                        <td><strong>{{ $item->book->title }}</strong></td>
                        <td>
                            @if ($item->is_returned)
                                <span class="success">Returned</span>
                            @elseif ($item->status === 'approved' && $item->isOverdue())
                                <span class="error">Overdue</span>
                            @else
                                {{ ucfirst($item->status) }}
                            @endif
                        </td>
                        <td>{{ $item->due_at?->format('M d, Y h:i A') ?? 'Not set' }}</td>
                        <td>
                            @if ($item->status === 'approved' && ! $item->is_returned)
                                @if ($item->return_requested_at)
                                    <span class="muted">Awaiting librarian confirmation</span>
                                @else
                                    <form method="POST" action="{{ route('return.request', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit">Request return</button>
                                    </form>
                                @endif
                            @elseif ($item->is_returned)
                                <span class="success">Returned {{ $item->returned_at?->format('M d, Y') }}</span>
                            @else
                                <span class="muted">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No requests yet. Browse the catalog to find a book.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="form-actions"><a class="btn secondary" href="{{ route('catalog') }}"><svg class="icon"><use href="#icon-book" /></svg>Explore the catalog</a></div>
    {{ $requests->links() }}
@endsection
