<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('car_profiles', function (Blueprint $table) {
            $table->json('additional_phone')->nullable()->comment('Stores additional phone numbers as JSON array');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_profiles', function (Blueprint $table) {
            $table->dropColumn('additional_phone');
        });
    }
};
