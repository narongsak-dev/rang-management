<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalSerial;
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

            // Return serials
            foreach ($rentalItem->rentalSerials()->whereNull('returned_at')->get() as $rs) {
                $rs->update(['returned_at' => now()]);
                $rs->productSerial->update(['status' => 'available']);
            }

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

    public function returnBySerial(Rental $rental, string $serialNo): RentalSerial
    {
        $rentalSerial = RentalSerial::whereNull('returned_at')
            ->whereHas('rentalItem', fn($q) => $q->where('rental_id', $rental->id))
            ->whereHas('productSerial', fn($q) => $q->where('serial_no', $serialNo))
            ->with(['rentalItem', 'productSerial'])
            ->firstOrFail();

        DB::transaction(function () use ($rentalSerial) {
            $rentalSerial->update(['returned_at' => now()]);
            $rentalSerial->productSerial->update(['status' => 'available']);

            $rentalItem = $rentalSerial->rentalItem;
            // Check if all serials in this item are returned
            $pendingSerials = RentalSerial::where('rental_item_id', $rentalItem->id)
                ->whereNull('returned_at')
                ->count();
            if ($pendingSerials === 0) {
                $rentalItem->update(['returned_at' => now()]);
                $rentalItem->product->increment('available_qty', $rentalItem->qty);
                $rental = $rentalItem->rental;
                if ($rental->isFullyReturned()) {
                    $rental->update(['status' => 'returned']);
                }
            }
        });

        return $rentalSerial;
    }
}
