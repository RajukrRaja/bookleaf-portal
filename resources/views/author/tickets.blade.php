@extends('layouts.app')
@section('title','My tickets')
@section('section','My tickets')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">HELP & SUPPORT</div>
            <h1>My tickets</h1>
            <p>All your conversations with the BookLeaf team.</p>
        </div>
        <a class="btn primary" href="{{ route('author.tickets.create') }}">＋ New ticket</a>
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
                        {{ $ticket->category }}
                        ·
                        {{ $ticket->created_at->format('d M Y') }}
                    </small>
                </div>
                <span class="pill {{ $ticket->status==='Resolved'?'green':'blue' }}">{{ $ticket->status }}</span>
                <span class="priority {{ strtolower($ticket->priority) }}">{{ $ticket->priority }}</span>
                <span>→</span>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-symbol">✉</div>
                <b>No tickets yet</b>
                <p>Need a hand? Our team is here to help.</p>
                <a class="btn primary" href="{{ route('author.tickets.create') }}">Raise your first ticket</a>
            </div>
        @endforelse
    </div>
@endsection
