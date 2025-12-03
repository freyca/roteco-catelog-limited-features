<?php

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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->string('address_type');
            $table->string('name')->maxLength(255);
            $table->string('surname')->maxLength(255);
            $table->string('bussiness_name')->nullable()->maxLength(255);
            $table->string('email')->maxLength(255);
            $table->string('financial_number')->nullable()->maxLength(20);
            $table->string('phone')->maxLength(20);
            $table->string('address')->maxLength(255);
            $table->string('city')->maxLength(255);
            $table->string('state')->maxLength(255);
            $table->unsignedInteger('zip_code')->maxLength(20);
            $table->string('country')->maxLength(255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
