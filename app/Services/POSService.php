<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalSerial;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSService
{
    public function __construct(
        private ReceiptService $receiptService,
        private AuditLogService $auditLogService,
    ) {}

    public function checkout(array $data, ?Customer $customer = null): Sale
    {
        return DB::transaction(function () use ($data, $customer) {
            $items    = $data['items'] ?? [];
            $subtotal = 0;
            $discount = (float) ($data['discount'] ?? 0);
            $hasRental = false;

            // Pre-validate stock
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                if ($item['is_rental']) {
                    if ($product->available_qty < $item['qty']) {
                        throw new \RuntimeException("จำนวนที่ว่างไม่เพียงพอสำหรับ [{$product->name}] มีเพียง {$product->available_qty}");
                    }
                    $hasRental = true;
                } else {
                    $unitType  = $item['unit_type'] ?? 'unit';
                    $needStock = ($unitType === 'box')
                        ? $item['qty'] * ($product->units_per_box ?: 1)
                        : $item['qty'];
                    if ($product->stock_qty < $needStock) {
                        throw new \RuntimeException("สต็อกไม่เพียงพอสำหรับ [{$product->name}] มีเพียง {$product->stock_qty}");
                    }
                }
            }

            // Calculate subtotal
            foreach ($items as $item) {
                $subtotal += $item['unit_price'] * $item['qty'];
            }
            $total = max(0, $subtotal - $discount);

            $sale = Sale::create([
                'order_no'       => $this->generateOrderNo(),
                'customer_id'    => $customer?->id,
                'staff_id'       => auth()->id(),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'paid_at'        => now(),
            ]);

            $rentalItemsData = [];

            foreach ($items as $item) {
                $product  = Product::findOrFail($item['product_id']);
                $unitType = $item['unit_type'] ?? 'unit';

                $lineTotal = $item['unit_price'] * $item['qty'];

                $saleItem = SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'qty'        => $item['qty'],
                    'unit_type'  => $unitType,
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                    'is_rental'  => $item['is_rental'],
                ]);

                if ($item['is_rental']) {
                    $product->decrement('available_qty', $item['qty']);
                    $rentalItemsData[] = [
                        'product'        => $product,
                        'qty'            => $item['qty'],
                        'deposit_amount' => $product->deposit ?? 0,
                        'serials'        => $item['serials'] ?? [],
                    ];
                } else {
                    if ($product->type === 'sale' || $product->type === 'service' || $product->type === 'fee') {
                        $stockReduce = ($unitType === 'box')
                            ? $item['qty'] * ($product->units_per_box ?: 1)
                            : $item['qty'];
                        $product->decrement('stock_qty', $stockReduce);
                    }
                }
            }

            if ($hasRental && $customer) {
                $rental = Rental::create([
                    'sale_id'     => $sale->id,
                    'customer_id' => $customer->id,
                    'rented_at'   => now(),
                    'status'      => 'open',
                ]);

                foreach ($rentalItemsData as $ri) {
                    $rentalItem = RentalItem::create([
                        'rental_id'      => $rental->id,
                        'product_id'     => $ri['product']->id,
                        'qty'            => $ri['qty'],
                        'deposit_amount' => $ri['deposit_amount'],
                    ]);

                    // Assign serials
                    foreach ($ri['serials'] as $sn) {
                        $ps = ProductSerial::where('serial_no', $sn)
                            ->where('product_id', $ri['product']->id)
                            ->where('status', 'available')
                            ->first();
                        if ($ps) {
                            $ps->update(['status' => 'rented']);
                            RentalSerial::create([
                                'rental_item_id'    => $rentalItem->id,
                                'product_serial_id' => $ps->id,
                                'rented_at'         => now(),
                            ]);
                        }
                    }
                }
            }

            $this->receiptService->generate($sale);
            $this->auditLogService->log('checkout', Sale::class, $sale->id, [
                'order_no' => $sale->order_no,
                'total'    => $sale->total,
            ]);

            return $sale->load(['items.product', 'customer', 'receipt']);
        });
    }

    private function generateOrderNo(): string
    {
        do {
            $no = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Sale::where('order_no', $no)->exists());

        return $no;
    }
}
