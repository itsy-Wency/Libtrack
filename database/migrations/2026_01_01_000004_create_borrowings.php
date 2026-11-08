<?php

// Track borrowing status and retain a reference to the student and book.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_id')->constrained()->restrictOnDelete();
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->timestamp('requested_at')->nullable();
            $t->timestamp('decided_at')->nullable();
            $t->string('librarian_note', 500)->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
