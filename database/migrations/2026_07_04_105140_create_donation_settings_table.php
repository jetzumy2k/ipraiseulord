<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bank', 'paypal', 'ewallet']);
            $table->string('label');
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('paypal_email')->nullable();
            $table->string('ewallet_provider')->nullable();
            $table->string('ewallet_number')->nullable();
            $table->text('instructions')->nullable();
            $table->json('display_locations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_settings');
    }
};
