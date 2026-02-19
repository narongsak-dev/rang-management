<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\POSService;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function __construct(private POSService $posService) {}

    public function index()
    {
        return view('pos.index');
    }

    public function scanProduct(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);
        $product = Product::active()->where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json(['found' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json(['found' => true, 'product' => $product]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.is_rental'  => 'required|boolean',
            'payment_method' => 'required|in:cash,card,transfer',
            'discount'       => 'nullable|numeric|min:0',
            'customer_id'    => 'nullable|exists:customers,id',
        ]);

        // Validate rental stock
        foreach ($request->items as $item) {
            if ($item['is_rental']) {
                $product = Product::find($item['product_id']);
                if ($product->available_qty < $item['qty']) {
                    return response()->json([
                        'error' => "Insufficient available qty for [{$product->name}]. Available: {$product->available_qty}",
                    ], 422);
                }
            }
        }

        $customer = $request->customer_id
            ? Customer::find($request->customer_id)
            : null;

        $sale = $this->posService->checkout($request->only([
            'items', 'payment_method', 'discount',
        ]), $customer);

        return response()->json([
            'success'    => true,
            'sale_id'    => $sale->id,
            'order_no'   => $sale->order_no,
            'receipt_url'=> route('pos.receipt', $sale->id),
        ]);
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'receipt', 'staff']);
        return view('pos.receipt', compact('sale'));
    }
}
