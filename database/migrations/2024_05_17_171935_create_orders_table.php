<?php

use App\Models\Address;
use App\Models\User;
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
            $table->ulid('id')->primary();
            $table->integer('purchase_cost');
            $table->string('payment_method');
            $table->string('status');
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->foreignIdFor(Address::class)->name('shipping_address_id')->constrained();
            $table->foreignIdFor(Address::class)->name('billing_address_id')->nullable()->constrained();
            $table->json('payment_gateway_response')->nullable();
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
