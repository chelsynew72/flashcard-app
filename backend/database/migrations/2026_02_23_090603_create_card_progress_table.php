<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->float('easiness_factor')->default(2.5);
            $table->integer('repetitions')->default(0);
            $table->integer('interval')->default(1);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->string('last_rating')->nullable();
            $table->unique(['user_id', 'card_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_progress');
    }
};
