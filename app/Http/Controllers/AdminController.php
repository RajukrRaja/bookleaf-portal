<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $r)
    {
        $query = Ticket::with(['author', 'book', 'assignee']);
        foreach (['status', 'category', 'priority'] as $f) if ($r->filled($f)) $query->where($f, $r->input($f));
        if ($r->filled('date')) $query->whereDate('created_at', $r->date);
        $tickets = $query->orderByRaw("FIELD(priority,'Critical','High','Medium','Low')")->orderBy('created_at')->paginate(15)->withQueryString();
        return view('admin.dashboard', [
            'tickets' => $tickets,
            'counts' => [
                'open' => Ticket::where('status', 'Open')->count(),
                'progress' => Ticket::where('status', 'In Progress')->count(),
                'resolved' => Ticket::where('status', 'Resolved')->count(),
                'all' => Ticket::count()
            ]
        ]);
    }
}
