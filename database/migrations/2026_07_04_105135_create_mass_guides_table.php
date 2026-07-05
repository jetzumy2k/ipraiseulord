<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mass_guides', function (Blueprint $table) {
            $table->id();
            $table->date('liturgical_date')->unique();
            $table->string('liturgical_season')->nullable();
            $table->string('liturgical_color')->nullable();
            $table->string('feast_name')->nullable();
            $table->string('first_reading_reference')->nullable();
            $table->text('first_reading_text')->nullable();
            $table->string('second_reading_reference')->nullable();
            $table->text('second_reading_text')->nullable();
            $table->string('responsorial_psalm_reference')->nullable();
            $table->text('responsorial_psalm_text')->nullable();
            $table->string('alleluia_reference')->nullable();
            $table->text('alleluia_text')->nullable();
            $table->string('gospel_reference')->nullable();
            $table->text('gospel_text')->nullable();
            $table->unsignedSmallInteger('liturgical_year')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_guides');
    }
};
