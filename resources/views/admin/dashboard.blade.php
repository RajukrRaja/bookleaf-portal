@extends('layouts.app')
@section('title','Ticket queue')
@section('section','Ticket queue')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">OPERATIONS WORKSPACE</div>
            <h1>Support inbox</h1>
            <p>Review, prioritize and resolve author conversations.</p>
        </div>
        <div class="inbox-count">
            <b>{{ $counts['all'] }}</b>
            <span>total tickets</span>
        </div>
    </div>
    <div class="stats-grid admin-stats">
        <div class="stat-card">
            <div class="stat-top">
                Open
                <span class="stat-icon amber">◷</span>
            </div>
            <strong>{{ $counts['open'] }}</strong>
            <small>Awaiting response</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                In progress
                <span class="stat-icon">↗</span>
            </div>
            <strong>{{ $counts['progress'] }}</strong>
            <small>Being handled</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                Resolved
                <span class="stat-icon green-text">✓</span>
            </div>
            <strong>{{ $counts['resolved'] }}</strong>
            <small>Successfully closed</small>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                All tickets
                <span class="stat-icon">▤</span>
            </div>
            <strong>{{ $counts['all'] }}</strong>
            <small>Across all authors</small>
        </div>
    </div>
    <div class="section-head">
        <div>
            <h2>All tickets</h2>
            <p>Oldest and highest priority tickets appear first.</p>
        </div>
    </div>
    <form class="filters panel" method="get">
        <select name="status">
            <option value="">All statuses</option>
            @foreach(['Open','In Progress','Resolved','Closed'] as $v)
                <option @selected(request('status')===$v)>{{ $v }}</option>
            @endforeach
        </select>
        <select name="category">
            <option value="">All categories</option>
            @foreach(['Royalty & Payments','ISBN & Metadata Issues','Printing & Quality','Distribution & Availability','Book Status & Production Updates','General Inquiry'] as $v)
                <option @selected(request('category')===$v)>{{ $v }}</option>
            @endforeach
        </select>
        <select name="priority">
            <option value="">All priorities</option>
            @foreach(['Critical','High','Medium','Low'] as $v)
                <option @selected(request('priority')===$v)>{{ $v }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}">
        <button class="btn primary">Filter</button>
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">Reset</a>
    </form>
    <div class="panel table-panel">
        <table>
            <thead>
                <tr>
                    <th>TICKET</th>
                    <th>AUTHOR</th>
                    <th>CATEGORY</th>
                    <th>PRIORITY</th>
                    <th>STATUS</th>
                    <th>ASSIGNED TO</th>
                    <th>CREATED</th>
                    <th>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>
                            <a class="ticket-link" href="{{ route('tickets.show',$t) }}">
                                <b>{{ $t->subject }}</b>
                                <small>
                                    {{ $t->ticket_number }}
                                    ·
                                    {{ $t->book?->title ?? 'General' }}
                                </small>
                            </a>
                        </td>
                        <td>{{ $t->author->name }}</td>
                        <td>{{ $t->category }}</td>
                        <td>
                            <span class="priority {{ strtolower($t->priority) }}">{{ $t->priority }}</span>
                        </td>
                        <td>
                            <span class="pill blue">{{ $t->status }}</span>
                        </td>
                        <td>{{ $t->assignee?->name ?? 'Unassigned' }}</td>
                        <td>{{ $t->created_at->format('d M, H:i') }}</td>
                        <td>
                            <a class="text-link" href="{{ route('tickets.show',$t) }}">Open →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">No tickets match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $tickets->links() }}
    </div>
@endsection
