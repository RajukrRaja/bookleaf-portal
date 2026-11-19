@extends('layouts.app')
@section('title','Raise a ticket')
@section('section','New support ticket')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">WE'RE HERE TO HELP</div>
            <h1>How can we help?</h1>
            <p>Tell us what's going on and our support team will get back to you.</p>
        </div>
    </div>
    <div class="panel form-panel">
        <form method="post" action="{{ route('author.tickets.store') }}" enctype="multipart/form-data" class="form-stack">
            @csrf
            <label>
                Related book
                <span class="optional">OPTIONAL</span>
                <select name="book_id">
                    <option value="">General / Account Level</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id')==$book->id)>
                            {{ $book->title }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                Subject
                <input name="subject" value="{{ old('subject') }}" placeholder="e.g. Question about my royalty payment" required maxlength="180">
            </label>
            <label>
                Describe your issue
                <textarea name="description" rows="6" placeholder="Share as much detail as you can. This helps us resolve your query faster..." required>{{ old('description') }}</textarea>
                <small class="field-hint">Please include relevant dates, order details or error messages where possible.</small>
            </label>
            <label>
                Attachment
                <span class="optional">OPTIONAL · MAX 5 MB</span>
                <input type="file" name="attachment" class="file-input">
            </label>
            <div class="form-actions">
                <a class="btn secondary" href="{{ route('author.tickets') }}">Cancel</a>
                <button class="btn primary">Submit ticket →</button>
            </div>
        </form>
    </div>
@endsection
