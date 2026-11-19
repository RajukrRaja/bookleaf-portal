<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Book;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'name' => 'BookLeaf Admin',
            'password' => Hash::make('Raju.45@'),
            'role' => 'admin'
        ]);
        $data = json_decode(file_get_contents(database_path('data/bookleaf_sample_data.json')), true);
        foreach ($data['authors'] as $a) {
            $u = User::updateOrCreate([
                'email' => $a['email']
            ], [
                'name' => $a['name'],
                'password' => Hash::make('Raju.45@'),
                'role' => 'author',
                'author_code' => $a['author_id'],
                'phone' => $a['phone'] ?? null,
                'city' => $a['city'] ?? null,
                'joined_date' => $a['joined_date'] ?? null
            ]);
            foreach ($a['books'] as $b) {
                Book::updateOrCreate([
                    'book_code' => $b['book_id']
                ], [
                    'author_id' => $u->id,
                    'title' => $b['title'],
                    'isbn' => $b['isbn'] ?? null,
                    'genre' => $b['genre'] ?? null,
                    'publication_date' => $b['publication_date'] ?? null,
                    'status' => $b['status'],
                    'mrp' => $b['mrp'] ?? 0,
                    'author_royalty_per_copy' => $b['author_royalty_per_copy'] ?? 0,
                    'total_copies_sold' => $b['total_copies_sold'] ?? 0,
                    'total_royalty_earned' => $b['total_royalty_earned'] ?? 0,
                    'royalty_paid' => $b['royalty_paid'] ?? 0,
                    'royalty_pending' => $b['royalty_pending'] ?? 0,
                    'last_royalty_payout_date' => $b['last_royalty_payout_date'] ?? null,
                    'print_partner' => $b['print_partner'] ?? null,
                    'available_on' => $b['available_on'] ?? []
                ]);
            }
        }
    }
}
