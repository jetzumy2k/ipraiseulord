<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novenas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['regular', 'common'])->default('common');
            $table->string('patron_saint')->nullable();
            $table->text('description')->nullable();
            $table->text('opening_prayer')->nullable();
            $table->text('closing_prayer')->nullable();
            $table->unsignedTinyInteger('duration_days')->default(9);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('visit_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novenas');
    }
};
