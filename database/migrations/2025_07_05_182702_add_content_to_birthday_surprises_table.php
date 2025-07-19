<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('birthday_surprises', function (Blueprint $table) {
            // Add missing columns that should have been in the original table
            $table->string('content')->nullable()->after('content_payload');
            $table->boolean('is_revealed')->default(false)->after('reveal_at');
        });
    }

    public function down(): void
    {
        Schema::table('birthday_surprises', function (Blueprint $table) {
            $table->dropColumn(['content', 'is_revealed']);
        });
    }
};
