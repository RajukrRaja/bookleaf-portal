@extends('layouts.app')
@section('title',$ticket->subject)
@section('section','Ticket details')
@section('content')
    <div class="backline">
        <a class="text-link" href="{{ auth()->user()->role==='admin'?route('admin.dashboard'):route('author.tickets') }}">
            ← Back to
            {{ auth()->user()->role==='admin'?'inbox':'my tickets' }}
        </a>
        <span class="pill blue">{{ $ticket->status }}</span>
    </div>
    <div class="detail-grid">
        <div class="detail-main">
            <div class="panel detail-panel">
                <div class="eyebrow">
                    {{ $ticket->ticket_number }}
                    ·
                    {{ $ticket->created_at->format('d M Y, H:i') }}
                </div>
                <h1>{{ $ticket->subject }}</h1>
                <p class="muted">
                    {{ $ticket->book?->title ?? 'General / Account Level' }}
                    ·
                    {{ $ticket->category }}
                </p>
                <div class="message-bubble author-bubble">
                    <b>{{ $ticket->author->name }}</b>
                    <p>{{ $ticket->description }}</p>
                </div>
                <div class="conversation" data-refresh-url="{{ route('tickets.refresh', $ticket) }}">
                    <h3>Conversation</h3>
                    @forelse($ticket->messages as $message)
                        <div data-message-id="{{ $message->id }}" class="message-bubble {{ $message->is_admin?'admin-bubble':'author-bubble' }}">
                            <b>
                                {{ $message->user->name }}
                                <small>{{ $message->created_at->format('d M, H:i') }}</small>
                            </b>
                            <p>{{ $message->message }}</p>
                        </div>
                    @empty
                        <p class="muted" data-empty>No replies yet. Your conversation will appear here.</p>
                    @endforelse
                </div>
                <form method="post" action="{{ route('tickets.reply',$ticket) }}" class="form-stack reply-form">
                    @csrf
                    <label>
                        {{ auth()->user()->role==='admin'?'Send a reply':'Write a reply' }}
                        <textarea name="message" rows="4" required placeholder="Write your message..."></textarea>
                    </label>
                    <button class="btn primary">Send reply →</button>
                </form>
            </div>
        </div>
        <aside class="detail-side">
            @if(auth()->user()->role==='admin')
                <div class="panel side-panel">
                    <h3>
                        AI assistant
                        <span class="ai-tag">✦ AI DRAFT</span>
                    </h3>
                    <p class="muted">Review this suggested response before sending.</p>
                    <form method="post" action="{{ route('admin.tickets.draft', $ticket) }}" class="form-stack">
                        @csrf
                        <label>
                            Reply tone
                            <select name="tone">
                                @foreach(\App\Services\BookLeafAiService::TONES as $tone)
                                    <option @selected(session('draft_tone', 'Professional') === $tone)>{{ $tone }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="btn secondary full">Generate draft</button>
                    </form>
                    <form method="post" action="{{ route('admin.tickets.send',$ticket) }}" class="form-stack">
                        @csrf
                        <textarea name="message" rows="9">{{ old('message',$ticket->ai_draft) }}</textarea>
                        <button class="btn primary full">Approve & send</button>
                    </form>
                    <div class="ai-meta">
                        <span>Category</span>
                        <b>{{ $ticket->category }}</b>
                        <span>Priority score</span>
                        <b>
                            {{ $ticket->priority_score }}
                            /100 ·
                            {{ $ticket->priority }}
                        </b>
                    </div>
                </div>
                <div class="panel side-panel">
                    <h3>Ticket controls</h3>
                    <form method="post" action="{{ route('admin.tickets.update',$ticket) }}" class="form-stack">
                        @csrf
                        @method('PATCH')
                        <label>
                            Status
                            <select name="status">
                                @foreach(['Open','In Progress','Resolved','Closed'] as $v)
                                    <option @selected($ticket->status===$v)>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            Category
                            <select name="category">
                                @foreach(['Royalty & Payments','ISBN & Metadata Issues','Printing & Quality','Distribution & Availability','Book Status & Production Updates','General Inquiry'] as $v)
                                    <option @selected($ticket->category===$v)>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            Priority
                            <select name="priority">
                                @foreach(['Critical','High','Medium','Low'] as $v)
                                    <option @selected($ticket->priority===$v)>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <button class="btn secondary full">Save changes</button>
                    </form>
                    <form method="post" action="{{ route('admin.tickets.assign',$ticket) }}" class="mt">
                        @csrf
                        <button class="btn secondary full">Assign to me</button>
                    </form>
                </div>
                <div class="panel side-panel">
                    <h3>Internal note</h3>
                    <form method="post" action="{{ route('admin.tickets.note',$ticket) }}" class="form-stack">
                        @csrf
                        <textarea name="note" rows="3" placeholder="Only visible to admins..."></textarea>
                        <button class="btn secondary full">Add internal note</button>
                    </form>
                    @foreach($ticket->notes as $note)
                        <div class="internal-note">
                            <b>{{ $note->user->name }}</b>
                            <p>{{ $note->note }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="panel side-panel">
                    <h3>Ticket details</h3>
                    <div class="ai-meta">
                        <span>Ticket ID</span>
                        <b>{{ $ticket->ticket_number }}</b>
                        <span>Category</span>
                        <b>{{ $ticket->category }}</b>
                        <span>Priority</span>
                        <b>{{ $ticket->priority }}</b>
                        <span>Created</span>
                        <b>{{ $ticket->created_at->format('d M Y') }}</b>
                    </div>
                </div>
            @endif
        </aside>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('ticket-conversation.js') }}" defer>
    </script>
@endpush
