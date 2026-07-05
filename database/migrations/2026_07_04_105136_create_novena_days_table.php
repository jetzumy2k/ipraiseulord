<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novena_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('novena_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number');
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('prayer')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['novena_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novena_days');
    }
};
