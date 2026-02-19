<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Sale;
use Illuminate\Support\Str;

class ReceiptService
{
    public function generate(Sale $sale): Receipt
    {
        return Receipt::firstOrCreate(
            ['sale_id' => $sale->id],
            [
                'receipt_no' => $this->generateReceiptNo(),
                'printed_at' => now(),
            ]
        );
    }

    private function generateReceiptNo(): string
    {
        do {
            $no = 'RCP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Receipt::where('receipt_no', $no)->exists());

        return $no;
    }
}
