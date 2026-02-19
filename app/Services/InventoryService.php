<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(private AuditLogService $auditLog) {}

    public function stockIn(Product $product, int $qty, string $note = ''): void
    {
        DB::transaction(function () use ($product, $qty, $note) {
            $product->increment('stock_qty', $qty);
            if ($product->isRentable()) {
                $product->increment('available_qty', $qty);
            }
            InventoryMovement::create([
                'product_id' => $product->id,
                'type'       => 'in',
                'qty'        => $qty,
                'note'       => $note,
                'created_by' => auth()->id(),
            ]);
            $this->auditLog->log('stock_in', Product::class, $product->id, [
                'qty' => $qty, 'note' => $note,
            ]);
        });
    }

    public function adjust(Product $product, int $newQty, string $note = ''): void
    {
        DB::transaction(function () use ($product, $newQty, $note) {
            $diff = $newQty - $product->stock_qty;
            $product->update(['stock_qty' => $newQty]);
            if ($product->isRentable()) {
                $newAvailable = max(0, $product->available_qty + $diff);
                $product->update(['available_qty' => $newAvailable]);
            }
            InventoryMovement::create([
                'product_id' => $product->id,
                'type'       => 'adjust',
                'qty'        => $diff,
                'note'       => $note,
                'created_by' => auth()->id(),
            ]);
            $this->auditLog->log('stock_adjust', Product::class, $product->id, [
                'new_qty' => $newQty, 'diff' => $diff, 'note' => $note,
            ]);
        });
    }
}
