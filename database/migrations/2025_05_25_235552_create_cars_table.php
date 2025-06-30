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
        Schema::create('cars', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['rent', 'sale']);
    $table->string('brand');
    $table->string('model');
    $table->decimal('price', 10, 2);
    $table->integer('mileage');
    $table->year('year');
    $table->string('transmission');
    $table->string('fuel');
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->boolean('enabled')->default(true);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
