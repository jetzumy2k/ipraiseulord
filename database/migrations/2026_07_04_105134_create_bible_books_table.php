<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_version_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->enum('testament', ['old', 'new']);
            $table->unsignedSmallInteger('book_order');
            $table->unsignedSmallInteger('chapter_count');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bible_version_id', 'book_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_books');
    }
};
