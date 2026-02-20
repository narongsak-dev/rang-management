<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'barcode', 'name', 'type', 'price', 'deposit',
        'stock_qty', 'available_qty', 'is_active',
        'price_per_unit', 'units_per_box', 'price_per_box', 'requires_serial',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'is_active' => 'boolean',
        'price_per_unit' => 'decimal:2',
        'units_per_box' => 'integer',
        'price_per_box' => 'decimal:2',
        'requires_serial' => 'boolean',
    ];

    public function serials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function isRentable(): bool
    {
        return $this->type === 'rent';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
