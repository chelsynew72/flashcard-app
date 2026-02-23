<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('deck_tag', function (Blueprint $table) {
            $table->foreignId('deck_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['deck_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deck_tag');
        Schema::dropIfExists('tags');
    }
};
