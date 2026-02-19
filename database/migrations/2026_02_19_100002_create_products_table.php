<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('name');
            $table->enum('type', ['sale', 'rent', 'service', 'fee']);
            $table->decimal('price', 10, 2);
            $table->decimal('deposit', 10, 2)->nullable();
            $table->integer('stock_qty')->default(0);
            $table->integer('available_qty')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
