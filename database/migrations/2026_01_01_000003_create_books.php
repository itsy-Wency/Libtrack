<?php

// Store book details, copy counts, cover paths, and category ownership.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->string('title');
            $t->string('author');
            $t->string('isbn')->nullable()->unique();
            $t->text('description')->nullable();
            $t->unsignedInteger('total_copies')->default(1);
            $t->unsignedInteger('available_copies')->default(1);
            $t->string('cover_path')->nullable();
            $t->timestamps();
            $t->index(['title', 'author']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
