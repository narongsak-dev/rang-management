<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rental_serials')) {
            Schema::create('rental_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rental_item_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_serial_id')->constrained()->cascadeOnDelete();
                $table->timestamp('rented_at');
                $table->timestamp('returned_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_serials');
    }
};
