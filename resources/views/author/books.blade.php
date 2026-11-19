@extends('layouts.app')
@section('title','My books')
@section('section','My books')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">YOUR CATALOGUE</div>
            <h1>My books</h1>
            <p>Track your titles, sales and royalty performance.</p>
        </div>
    </div>
    <div class="panel table-panel">
        <table>
            <thead>
                <tr>
                    <th>BOOK</th>
                    <th>ISBN</th>
                    <th>PUBLICATION</th>
                    <th>STATUS</th>
                    <th>MRP</th>
                    <th>SOLD</th>
                    <th>EARNED</th>
                    <th>PAID</th>
                    <th>PENDING</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $b)
                    <tr>
                        <td>
                            <b>{{ $b->title }}</b>
                            <small>{{ $b->genre }}</small>
                        </td>
                        <td>{{ $b->isbn }}</td>
                        <td>{{ $b->publication_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <span class="pill green">{{ $b->status }}</span>
                        </td>
                        <td>
                            ₹
                            {{ number_format($b->mrp) }}
                        </td>
                        <td>{{ number_format($b->total_copies_sold) }}</td>
                        <td>
                            ₹
                            {{ number_format($b->total_royalty_earned) }}
                        </td>
                        <td>
                            ₹
                            {{ number_format($b->royalty_paid) }}
                        </td>
                        <td class="amount">
                            ₹
                            {{ number_format($b->royalty_pending) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">No books available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
