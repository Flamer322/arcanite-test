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

            $table->foreignId('user_id')
                ->references("id")
                ->on("users")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('unit_id');
            $table->string('api_key');

            $table->timestamps();

            $table->unique(['user_id', 'unit_id']);
        });

        Schema::create('processed_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->references("id")
                ->on("subscriptions")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('order_id');

            $table->timestamps();

            $table->unique(['subscription_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_orders');
        Schema::dropIfExists('subscriptions');
    }
};
