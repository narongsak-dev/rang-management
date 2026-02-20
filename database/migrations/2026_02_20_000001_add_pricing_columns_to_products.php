<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price_per_unit')) {
                $table->decimal('price_per_unit', 12, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'units_per_box')) {
                $table->integer('units_per_box')->default(0)->after('price_per_unit');
            }
            if (!Schema::hasColumn('products', 'price_per_box')) {
                $table->decimal('price_per_box', 12, 2)->default(0)->after('units_per_box');
            }
            if (!Schema::hasColumn('products', 'requires_serial')) {
                $table->boolean('requires_serial')->default(false)->after('price_per_box');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_per_unit', 'units_per_box', 'price_per_box', 'requires_serial']);
        });
    }
};
