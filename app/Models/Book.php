<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = [
        'book_code',
        'author_id',
        'title',
        'isbn',
        'genre',
        'publication_date',
        'status',
        'mrp',
        'author_royalty_per_copy',
        'total_copies_sold',
        'total_royalty_earned',
        'royalty_paid',
        'royalty_pending',
        'last_royalty_payout_date',
        'print_partner',
        'available_on'
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'last_royalty_payout_date' => 'date',
            'available_on' => 'array',
            'mrp' => 'decimal:2'
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
