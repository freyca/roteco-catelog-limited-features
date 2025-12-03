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
        Schema::create('product_complements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ean13')->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->integer('price');
            $table->integer('price_with_discount')->nullable();
            $table->integer('price_when_user_owns_product')->nullable();
            $table->boolean('published')->default(false)->index();
            $table->unsignedInteger('stock');
            $table->float('dimension_length');
            $table->float('dimension_width');
            $table->float('dimension_height');
            $table->float('dimension_weight');
            $table->string('slogan');
            $table->string('meta_description');
            $table->text('short_description');
            $table->text('description');
            $table->string('main_image');
            $table->json('images');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_complements');
    }
};
