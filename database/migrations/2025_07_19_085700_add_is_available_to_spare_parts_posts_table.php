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
        Schema::table('spare_parts_posts', function (Blueprint $table) {
            $table->boolean('is_available')->default(1)->after('spare_parts_subcategory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spare_parts_posts', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
};
