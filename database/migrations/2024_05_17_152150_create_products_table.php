<?php

use App\Models\Category;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            // $table->integer('price');
            // $table->integer('price_with_discount')->nullable();
            $table->boolean('published')->default(false)->index();
            // $table->unsignedInteger('stock');
            // $table->boolean('can_be_assembled')->default(false);
            // $table->boolean('mandatory_assembly')->default(false);
            // $table->integer('assembly_price')->default(0);
            // $table->float('dimension_length');
            // $table->float('dimension_width');
            // $table->float('dimension_height');
            // $table->float('dimension_weight');
            // $table->string('slogan');
            // $table->string('meta_description');
            // $table->text('short_description');
            // $table->text('description');
            $table->string('main_image');
            // $table->json('images');
            $table->foreignIdFor(Category::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
