<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiestas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedTinyInteger('month')->nullable();
            $table->unsignedTinyInteger('day')->nullable();
            $table->string('category');
            $table->string('honoree_name')->nullable();
            $table->text('description')->nullable();
            $table->string('liturgical_rank')->nullable();
            $table->boolean('is_movable')->default(false);
            $table->string('movable_rule')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['month', 'day', 'title']);
            $table->unique(['movable_rule', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiestas');
    }
};
