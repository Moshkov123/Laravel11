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
        Schema::create('product__photos', function (Blueprint $table) {
            $table->id();
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product__photos');
    }
};
// $table->unsignedBigInteger('product_id');
// $table->foreign('product_id')->references('id')->on('products');
// $table->unsignedBigInteger('photos_id');
// $table->foreign('photos_id')->references('id')->on('photos');