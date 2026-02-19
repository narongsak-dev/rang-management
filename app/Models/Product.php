<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'barcode', 'name', 'type', 'price', 'deposit',
        'stock_qty', 'available_qty', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

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
