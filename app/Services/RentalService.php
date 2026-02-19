<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Support\Facades\DB;

class RentalService
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function returnItem(RentalItem $rentalItem): void
    {
        DB::transaction(function () use ($rentalItem) {
            $rentalItem->update(['returned_at' => now()]);
            $product = $rentalItem->product;
            $product->increment('available_qty', $rentalItem->qty);

            $rental = $rentalItem->rental;
            if ($rental->isFullyReturned()) {
                $rental->update(['status' => 'returned']);
            }

            $this->auditLogService->log('rental_return', RentalItem::class, $rentalItem->id, [
                'product_id' => $product->id,
                'qty'        => $rentalItem->qty,
            ]);
        });
    }

    public function returnByBarcode(Rental $rental, string $barcode): RentalItem
    {
        $product = Product::where('barcode', $barcode)->firstOrFail();

        $rentalItem = RentalItem::where('rental_id', $rental->id)
            ->where('product_id', $product->id)
            ->whereNull('returned_at')
            ->firstOrFail();

        $this->returnItem($rentalItem);

        return $rentalItem;
    }
}
