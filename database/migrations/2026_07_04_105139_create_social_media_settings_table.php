<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url')->nullable();
            $table->string('handle')->nullable();
            $table->json('display_locations')->nullable();
            $table->boolean('show_share_buttons')->default(true);
            $table->boolean('show_follow_links')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_settings');
    }
};
