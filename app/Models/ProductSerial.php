<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSerial extends Model
{
    protected $fillable = ['product_id', 'serial_no', 'status', 'note'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rentalSerials(): HasMany
    {
        return $this->hasMany(RentalSerial::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
