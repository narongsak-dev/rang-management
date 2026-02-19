<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
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
            $items = $data['items'] ?? [];
            $subtotal = 0;
            $discount = (float) ($data['discount'] ?? 0);
            $hasRental = false;

            foreach ($items as $item) {
                $subtotal += $item['unit_price'] * $item['qty'];
                if ($item['is_rental']) {
                    $hasRental = true;
                }
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
                $product = Product::findOrFail($item['product_id']);

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'qty'        => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['unit_price'] * $item['qty'],
                    'is_rental'  => $item['is_rental'],
                ]);

                if ($item['is_rental']) {
                    $product->decrement('available_qty', $item['qty']);
                    $rentalItemsData[] = [
                        'product'        => $product,
                        'qty'            => $item['qty'],
                        'deposit_amount' => $product->deposit ?? 0,
                    ];
                } elseif ($product->type === 'sale') {
                    $product->decrement('stock_qty', $item['qty']);
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
                    RentalItem::create([
                        'rental_id'      => $rental->id,
                        'product_id'     => $ri['product']->id,
                        'qty'            => $ri['qty'],
                        'deposit_amount' => $ri['deposit_amount'],
                    ]);
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
