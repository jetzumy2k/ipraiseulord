<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['embed', 'manual'])->default('manual');
            $table->text('embed_script')->nullable();
            $table->string('image_path')->nullable();
            $table->string('url')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->json('placements')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('impression_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
