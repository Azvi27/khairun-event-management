<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('birthday_surprises', function (Blueprint $table) {
            $table->boolean('is_revealed')->default(false);
            $table->timestamp('reveal_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birthday_surprises', function (Blueprint $table) {
            $table->dropColumn(['is_revealed', 'reveal_at']);
        });
    }
};