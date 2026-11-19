@extends('layouts.app')
@section('title','Author overview')
@section('section','Overview')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">YOUR PUBLISHING SPACE</div>
            <h1>
                Good to see you,
                {{ explode(' ',auth()->user()->name)[0] }}
                <span>✦</span>
            </h1>
            <p>Here's what's happening with your books.</p>
        </div>
        <a class="btn primary" href="{{ route('author.tickets.create') }}">＋ Raise a ticket</a>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                Total books
                <span class="stat-icon">▤</span>
            </div>
            <strong>{{ $books->count() }}</strong>
            <small>Across your catalogue</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                Copies sold
                <span class="stat-icon">↗</span>
            </div>
            <strong>{{ number_format($books->sum('total_copies_sold')) }}</strong>
            <small>Total lifetime sales</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                Royalty earned
                <span class="stat-icon">₹</span>
            </div>
            <strong>
                ₹
                {{ number_format($earned) }}
            </strong>
            <small>Across all titles</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                Pending royalty
                <span class="stat-icon amber">◷</span>
            </div>
            <strong>
                ₹
                {{ number_format($pending) }}
            </strong>
            <small>Awaiting payout</small>
        </div>
    </div>
    <div class="section-head">
        <div>
            <h2>Your books</h2>
            <p>A quick look at your published titles</p>
        </div>
        <a class="text-link" href="{{ route('author.books') }}">View all books →</a>
    </div>
    <div class="panel table-panel">
        <table>
            <thead>
                <tr>
                    <th>BOOK TITLE</th>
                    <th>STATUS</th>
                    <th>COPIES SOLD</th>
                    <th>ROYALTY EARNED</th>
                    <th>PENDING</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books->take(5) as $book)
                    <tr>
                        <td>
                            <b>{{ $book->title }}</b>
                            <small>{{ $book->genre }}</small>
                        </td>
                        <td>
                            <span class="pill green">{{ $book->status }}</span>
                        </td>
                        <td>{{ number_format($book->total_copies_sold) }}</td>
                        <td>
                            ₹
                            {{ number_format($book->total_royalty_earned) }}
                        </td>
                        <td class="amount">
                            ₹
                            {{ number_format($book->royalty_pending) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">No books found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="section-head">
        <div>
            <h2>Recent support tickets</h2>
            <p>Stay up to date with your conversations</p>
        </div>
        <a class="text-link" href="{{ route('author.tickets') }}">All tickets →</a>
    </div>
    <div class="panel ticket-list">
        @forelse($tickets as $ticket)
            <a class="ticket-row" href="{{ route('tickets.show',$ticket) }}">
                <div class="ticket-icon">✉</div>
                <div class="ticket-main">
                    <b>{{ $ticket->subject }}</b>
                    <small>
                        {{ $ticket->ticket_number }}
                        ·
                        {{ $ticket->created_at->format('d M Y') }}
                    </small>
                </div>
                <span class="pill {{ $ticket->status==='Resolved'?'green':'blue' }}">{{ $ticket->status }}</span>
                <span>→</span>
            </a>
        @empty
            <div class="empty">You haven't raised any tickets yet.</div>
        @endforelse
    </div>
@endsection
