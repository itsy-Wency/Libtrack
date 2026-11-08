{{-- Librarian queue: review pending requests and confirm returned books. --}}
@extends('layouts.app')

@section('title', 'Borrowing Requests | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Librarian workspace</p>
            <h1>Borrowing requests</h1>
            <p class="muted">Review requests, confirm returns, and keep readers informed.</p>
        </div>
        <span class="detail-item"><strong>Borrowing period</strong>3 calendar days from approval</span>
    </div>

    <section class="card table-wrap" aria-label="Borrowing and return requests">
        <table>
            <thead><tr><th>Student</th><th>Book</th><th>Status and due date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($requests as $item)
                    <tr>
                        <td><strong>{{ $item->user->name }}</strong><br><span class="muted">{{ $item->user->email }}</span></td>
                        <td>{{ $item->book->title }}</td>
                        <td>
                            @if ($item->is_returned)
                                <span class="success">Returned</span><br>{{ $item->returned_at?->format('M d, Y') }}
                            @elseif ($item->status === 'approved' && $item->isOverdue())
                                <span class="error">Overdue</span><br>Due {{ $item->due_at?->format('M d, Y') }}
                            @else
                                {{ ucfirst($item->status) }}
                                @if ($item->due_at)<br><span class="muted">Due {{ $item->due_at->format('M d, Y') }}</span>@endif
                            @endif
                        </td>
                        <td>
                            <div class="action-stack">
                                @if ($item->status === 'pending')
                                    <form method="POST" action="{{ route('lib.decision', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="approved">
                                        <input name="librarian_note" placeholder="Optional note" aria-label="Optional approval note">
                                        <button type="submit"><svg class="icon"><use href="#icon-check" /></svg>Approve request</button>
                                    </form>
                                    <form method="POST" action="{{ route('lib.decision', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="rejected">
                                        <input name="librarian_note" placeholder="Optional reason" aria-label="Optional rejection reason">
                                        <button class="danger" type="submit">Decline request</button>
                                    </form>
                                @elseif ($item->status === 'approved' && ! $item->is_returned)
                                    <form method="POST" action="{{ route('lib.remind', $item) }}">
                                        @csrf
                                        <button class="secondary" type="submit">Send return reminder</button>
                                    </form>
                                    @if ($item->return_requested_at)
                                        <p class="muted">Student requested return. Verify the physical book.</p>
                                        <form method="POST" action="{{ route('lib.confirm-return', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"><svg class="icon"><use href="#icon-check" /></svg>Confirm return</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="muted">No action required.</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No borrowing requests to review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{ $requests->links() }}
@endsection
