<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('users', function(Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->enum('role', ['author', 'admin'])->default('author');
            $t->string('author_code')->nullable()->unique();
            $t->string('phone')->nullable();
            $t->string('city')->nullable();
            $t->date('joined_date')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('books', function(Blueprint $t) {
            $t->id();
            $t->string('book_code')->unique();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->string('isbn')->nullable();
            $t->string('genre')->nullable();
            $t->date('publication_date')->nullable();
            $t->string('status');
            $t->decimal('mrp', 10, 2)->default(0);
            $t->decimal('author_royalty_per_copy', 10, 2)->default(0);
            $t->unsignedInteger('total_copies_sold')->default(0);
            $t->decimal('total_royalty_earned', 12, 2)->default(0);
            $t->decimal('royalty_paid', 12, 2)->default(0);
            $t->decimal('royalty_pending', 12, 2)->default(0);
            $t->date('last_royalty_payout_date')->nullable();
            $t->string('print_partner')->nullable();
            $t->json('available_on')->nullable();
            $t->timestamps();
        });
        Schema::create('tickets', function(Blueprint $t) {
            $t->id();
            $t->string('ticket_number')->unique();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $t->string('subject');
            $t->text('description');
            $t->string('category')->default('General Inquiry');
            $t->enum('priority', ['Critical', 'High', 'Medium', 'Low'])->default('Medium');
            $t->unsignedTinyInteger('priority_score')->default(50);
            $t->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $t->longText('ai_draft')->nullable();
            $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'priority', 'category']);
        });
        Schema::create('ticket_messages', function(Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->longText('message');
            $t->boolean('is_admin')->default(false);
            $t->timestamps();
        });
        Schema::create('ticket_notes', function(Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->text('note');
            $t->timestamps();
        });
        Schema::create('sessions', function(Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_notes');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('books');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
