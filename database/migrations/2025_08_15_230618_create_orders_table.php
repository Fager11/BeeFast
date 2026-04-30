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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('delivery_price', 10, 2);
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending', 'in_progress', 'on_the_way', 'delivered', 'cancelled'])->default('pending');
    $table->timestamp('estimated_delivery_time')->nullable();
    $table->string('address')->nullable();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
