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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id'); // user who clicked the "turn on notification"
            $table->unsignedBigInteger('subscribed_to_id'); // user they are following
            $table->timestamps();

            $table->foreign('subscriber_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscribed_to_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['subscriber_id', 'subscribed_to_id']); // Prevent duplicate subscriptions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
