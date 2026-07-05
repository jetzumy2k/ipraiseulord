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
            $table->string('title', 100);
            $table->unsignedTinyInteger('month')->nullable();
            $table->unsignedTinyInteger('day')->nullable();
            $table->string('category');
            $table->string('honoree_name')->nullable();
            $table->text('description')->nullable();
            $table->string('liturgical_rank')->nullable();
            $table->boolean('is_movable')->default(false);
            $table->string('movable_rule', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['month', 'day', 'title']);
            $table->unique(['movable_rule', 'title'], 'fiestas_movable_title_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiestas');
    }
};
