<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_chapter_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('verse_number');
            $table->text('text');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bible_chapter_id', 'verse_number']);
            $table->fullText('text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_verses');
    }
};
