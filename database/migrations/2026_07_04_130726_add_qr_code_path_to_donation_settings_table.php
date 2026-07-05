<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_settings', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('ewallet_number');
        });
    }

    public function down(): void
    {
        Schema::table('donation_settings', function (Blueprint $table) {
            $table->dropColumn('qr_code_path');
        });
    }
};
