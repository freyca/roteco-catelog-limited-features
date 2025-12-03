<?php

use App\Models\ProductComplement;
use App\Models\ProductFeatureValue;
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
        Schema::create('product_complement_product_feature_value', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductComplement::class)->constrained(indexName: 'product_comp_product_feat_val_product_comp_id_foreign');
            $table->foreignIdFor(ProductFeatureValue::class)->constrained(indexName: 'prod_comp_prod_feat_val_product_feat_val_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_complement_product_feature');
    }
};
