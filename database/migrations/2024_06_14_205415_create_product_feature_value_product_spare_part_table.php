<?php

use App\Models\ProductFeatureValue;
use App\Models\ProductSparePart;
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
        Schema::create('product_feature_value_product_spare_part', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductSparePart::class)->constrained(indexName: 'prod_feat_val_prod_spare_part_prod_spare_part_id_foreign');
            $table->foreignIdFor(ProductFeatureValue::class)->constrained(indexName: 'prod_feat_val_prod_spare_part_prod_feat_val_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_feature_product_spare_part');
    }
};
