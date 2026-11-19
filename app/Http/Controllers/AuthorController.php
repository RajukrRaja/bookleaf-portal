<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function dashboard(Request $r)
    {
        $books = $r->user()->books;
        return view('author.dashboard', [
            'books' => $books,
            'tickets' => $r->user()->tickets()->latest()->take(5)->get(),
            'earned' => $books->sum('total_royalty_earned'),
            'paid' => $books->sum('royalty_paid'),
            'pending' => $books->sum('royalty_pending')
        ]);
    }

    public function books(Request $r)
    {
        return view('author.books', [
            'books' => $r->user()->books()->latest()->get()
        ]);
    }

    public function tickets(Request $r)
    {
        $tickets = $r->user()->tickets()->with(['book', 'messages.user'])->latest()->get();
        return view('author.tickets', compact('tickets'));
    }
}
