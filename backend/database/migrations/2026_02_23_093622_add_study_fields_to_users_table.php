<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('study_streak')->default(0);
            $table->date('last_studied_at')->nullable();
            $table->integer('daily_card_limit')->default(20);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['study_streak', 'last_studied_at', 'daily_card_limit']);
        });
    }
};
