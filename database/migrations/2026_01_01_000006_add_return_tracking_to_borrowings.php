<?php

// Add due dates, return requests, and confirmation fields to existing borrowings.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->boolean('is_returned')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['due_at', 'return_requested_at', 'returned_at', 'is_returned']);
        });
    }
};
