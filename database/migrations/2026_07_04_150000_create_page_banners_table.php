<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_banners', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique();
            $table->string('label');
            $table->string('image_path')->nullable();
            $table->decimal('overlay_opacity', 3, 2)->default(0.55);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_banners');
    }
};
