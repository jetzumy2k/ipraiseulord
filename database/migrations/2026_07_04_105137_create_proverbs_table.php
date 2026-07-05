<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proverbs', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('reference')->nullable();
            $table->string('bible_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('visit_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proverbs');
    }
};
