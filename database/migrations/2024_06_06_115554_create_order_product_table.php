<?php

use App\Models\ProductVariant;
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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('order_id')->constrained();
            $table->unsignedInteger('orderable_id');
            $table->foreignIdFor(ProductVariant::class)->nullable()->constrained();
            $table->string('orderable_type');
            $table->integer('unit_price');
            $table->integer('assembly_price')->default(0)->nullable();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
