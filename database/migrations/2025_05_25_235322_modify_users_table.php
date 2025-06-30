<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('name')->nullable()->change();
        $table->string('phone')->nullable();
        $table->string('additional_phone')->nullable();
        $table->string('city')->nullable();
        $table->enum('role', ['car_seller', 'spare_parts_seller', 'tow_truck', 'garage_owner'])->nullable();
        $table->string('google_id')->unique()->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'additional_phone', 'city', 'role', 'google_id']);
    });
}

};
