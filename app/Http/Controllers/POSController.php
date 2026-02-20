<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Services\POSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class POSController extends Controller
{
    public function __construct(private POSService $posService) {}

    public function index()
    {
        return view('pos.index');
    }

    public function cart()
    {
        return response()->json([
            'cart'     => Session::get('pos_cart', []),
            'customer' => Session::get('pos_customer'),
        ]);
    }

    public function cartAdd(Request $request)
    {
        $request->validate([
            'barcode'   => 'required|string',
            'qty'       => 'nullable|integer|min:1',
            'unit_type' => 'nullable|in:unit,box',
            'mode'      => 'nullable|in:sale,rent',
        ]);

        $product = Product::active()->where('barcode', $request->barcode)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'ไม่พบสินค้า'], 404);
        }

        $qty      = max(1, (int) $request->input('qty', 1));
        $unitType = $request->input('unit_type', 'unit');
        $mode     = $request->input('mode', $product->type === 'rent' ? 'rent' : 'sale');
        $isRental = ($mode === 'rent');

        // Validate box mode
        if ($unitType === 'box') {
            if (!$product->units_per_box || !$product->price_per_box) {
                return response()->json(['success' => false, 'message' => 'สินค้านี้ไม่รองรับการขายยกกล่อง'], 422);
            }
        }

        // Check stock
        if ($isRental) {
            if ($product->available_qty < $qty) {
                return response()->json(['success' => false, 'message' => 'จำนวนที่ว่างไม่เพียงพอ (มี ' . $product->available_qty . ')'], 422);
            }
        } else {
            $needStock = ($unitType === 'box') ? $qty * $product->units_per_box : $qty;
            if ($product->stock_qty < $needStock) {
                return response()->json(['success' => false, 'message' => 'สต็อกไม่เพียงพอ (มี ' . $product->stock_qty . ')'], 422);
            }
        }

        // Unit price
        if ($isRental) {
            $unitPrice = (float) ($product->price_per_unit ?: $product->price);
        } elseif ($unitType === 'box') {
            $unitPrice = (float) $product->price_per_box;
        } else {
            $unitPrice = (float) ($product->price_per_unit ?: $product->price);
        }

        $cart = Session::get('pos_cart', []);

        // Find existing item
        $key = null;
        foreach ($cart as $k => $item) {
            if ($item['product_id'] === $product->id
                && $item['unit_type'] === $unitType
                && $item['is_rental'] === $isRental) {
                $key = $k;
                break;
            }
        }

        if ($key !== null) {
            $cart[$key]['qty'] += $qty;
            $cart[$key]['line_total'] = $cart[$key]['unit_price'] * $cart[$key]['qty'];
        } else {
            $cart[] = [
                'id'           => uniqid('ci_'),
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'barcode'      => $product->barcode,
                'qty'          => $qty,
                'unit_type'    => $unitType,
                'unit_price'   => $unitPrice,
                'line_total'   => $unitPrice * $qty,
                'is_rental'    => $isRental,
                'requires_serial' => (bool) $product->requires_serial,
                'available_qty'=> $product->available_qty,
                'serials'      => [],
            ];
        }

        Session::put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'เพิ่มสินค้าแล้ว',
            'cart'    => $cart,
        ]);
    }

    public function cartUpdate(Request $request)
    {
        $request->validate([
            'line_id' => 'required|string',
            'qty'     => 'required|integer|min:1',
        ]);

        $cart = Session::get('pos_cart', []);
        foreach ($cart as &$item) {
            if ($item['id'] === $request->line_id) {
                $item['qty']        = (int) $request->qty;
                $item['line_total'] = $item['unit_price'] * $item['qty'];
                break;
            }
        }
        Session::put('pos_cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function cartRemove(Request $request)
    {
        $request->validate(['line_id' => 'required|string']);

        $cart = Session::get('pos_cart', []);
        $cart = array_values(array_filter($cart, fn($i) => $i['id'] !== $request->line_id));
        Session::put('pos_cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function cartSerials(Request $request)
    {
        $request->validate([
            'line_id' => 'required|string',
            'serials' => 'required|array',
            'serials.*' => 'string',
        ]);

        $cart = Session::get('pos_cart', []);
        foreach ($cart as &$item) {
            if ($item['id'] === $request->line_id) {
                // Validate serials
                $serials = array_unique($request->serials);
                if (count($serials) !== $item['qty']) {
                    return response()->json(['success' => false, 'message' => 'จำนวน Serial ไม่ครบตามที่ต้องการ'], 422);
                }
                foreach ($serials as $sn) {
                    $ps = ProductSerial::where('serial_no', $sn)
                        ->where('product_id', $item['product_id'])
                        ->where('status', 'available')
                        ->first();
                    if (!$ps) {
                        return response()->json(['success' => false, 'message' => 'Serial ' . $sn . ' ไม่พร้อมให้เช่า'], 422);
                    }
                }
                $item['serials'] = array_values($serials);
                break;
            }
        }
        Session::put('pos_cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function checkout(Request $request)
    {
        $cart     = Session::get('pos_cart', []);
        $customer = Session::get('pos_customer');

        // Allow items to be passed directly in the request (e.g. API/testing usage)
        if (empty($cart) && $request->has('items')) {
            $request->validate([
                'payment_method'   => 'required|in:cash,card,transfer',
                'discount'         => 'nullable|numeric|min:0',
                'customer_id'      => 'nullable|exists:customers,id',
                'items'            => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.qty'      => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.is_rental'  => 'required|boolean',
            ]);
            $customerId    = $request->input('customer_id') ?? ($customer ? $customer['id'] : null);
            $customerModel = $customerId ? Customer::find($customerId) : null;
            $requestItems  = collect($request->input('items'))->map(fn($i) => array_merge([
                'unit_type' => 'unit',
                'line_total' => $i['unit_price'] * $i['qty'],
                'serials'   => [],
            ], $i))->all();

            $hasRental = collect($requestItems)->contains('is_rental', true);
            if ($hasRental && !$customerId) {
                return response()->json(['success' => false, 'error' => 'กรุณาระบุลูกค้าก่อนทำรายการเช่า'], 422);
            }

            try {
                $sale = $this->posService->checkout([
                    'items'          => $requestItems,
                    'payment_method' => $request->payment_method,
                    'discount'       => $request->input('discount', 0),
                ], $customerModel);
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }

            return response()->json([
                'success'     => true,
                'sale_id'     => $sale->id,
                'order_no'    => $sale->order_no,
                'receipt_url' => route('pos.receipt', $sale->id),
            ]);
        }

        if (empty($cart)) {
            return response()->json(['success' => false, 'error' => 'ไม่มีสินค้าในตะกร้า'], 422);
        }

        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
            'discount'       => 'nullable|numeric|min:0',
            'customer_id'    => 'nullable|exists:customers,id',
        ]);

        // Check rental requires customer
        $hasRental = collect($cart)->contains('is_rental', true);
        $customerId = $request->input('customer_id') ?? ($customer ? $customer['id'] : null);

        if ($hasRental && !$customerId) {
            return response()->json(['success' => false, 'error' => 'กรุณาระบุลูกค้าก่อนทำรายการเช่า'], 422);
        }

        // Check serials for rental
        foreach ($cart as $item) {
            if ($item['is_rental'] && ($item['requires_serial'] ?? false)) {
                if (count($item['serials'] ?? []) !== $item['qty']) {
                    return response()->json(['success' => false, 'error' => 'กรุณาระบุ Serial Number ของ ' . $item['product_name'] . ' ให้ครบ'], 422);
                }
            }
        }

        // Build items array for POSService
        $items = array_map(fn($item) => [
            'product_id'  => $item['product_id'],
            'qty'         => $item['qty'],
            'unit_type'   => $item['unit_type'],
            'unit_price'  => $item['unit_price'],
            'line_total'  => $item['line_total'],
            'is_rental'   => $item['is_rental'],
            'serials'     => $item['serials'] ?? [],
        ], $cart);

        $customerModel = $customerId ? Customer::find($customerId) : null;

        try {
            $sale = $this->posService->checkout([
                'items'          => $items,
                'payment_method' => $request->payment_method,
                'discount'       => $request->input('discount', 0),
            ], $customerModel);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }

        Session::forget(['pos_cart', 'pos_customer']);

        return response()->json([
            'success'     => true,
            'sale_id'     => $sale->id,
            'order_no'    => $sale->order_no,
            'receipt_url' => route('pos.receipt', $sale->id),
        ]);
    }

    // Legacy scan endpoint (keep for backward compat)
    public function scanProduct(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);
        $product = Product::active()->where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json(['found' => false, 'message' => 'ไม่พบสินค้า'], 404);
        }

        return response()->json(['found' => true, 'product' => $product]);
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'receipt', 'staff']);
        return view('pos.receipt', compact('sale'));
    }

    public function setCustomer(Request $request)
    {
        $request->validate([
            'citizen_id' => 'required|string|size:13|regex:/^[0-9]+$/',
        ]);

        $customer = Customer::with('activeMembership')
            ->where('citizen_id', $request->citizen_id)
            ->first();

        if (!$customer) {
            return response()->json(['found' => false, 'message' => 'ไม่พบลูกค้า']);
        }

        Session::put('pos_customer', [
            'id'               => $customer->id,
            'name'             => $customer->name,
            'citizen_id'       => $customer->citizen_id,
            'active_membership'=> $customer->activeMembership ? true : false,
        ]);

        return response()->json([
            'found'    => true,
            'customer' => [
                'id'               => $customer->id,
                'name'             => $customer->name,
                'citizen_id'       => $customer->citizen_id,
                'active_membership'=> $customer->activeMembership,
            ],
        ]);
    }

    public function clearCustomer()
    {
        Session::forget('pos_customer');
        return response()->json(['success' => true]);
    }
}
