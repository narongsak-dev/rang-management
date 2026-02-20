<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\RentalSerial;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function index(Request $request)
    {
        $query = ProductSerial::with('product');
        if ($request->filled('search')) {
            $query->where('serial_no', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        $serials = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $products = Product::where('requires_serial', true)->orderBy('name')->get();
        return view('product-serials.index', compact('serials', 'products'));
    }

    public function create()
    {
        $products = Product::where('requires_serial', true)->orderBy('name')->get();
        return view('product-serials.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'serial_no'  => 'required|string|unique:product_serials',
            'status'     => 'required|in:available,rented,maintenance,lost',
            'note'       => 'nullable|string',
        ]);
        $serial = ProductSerial::create($data);
        $this->auditLogService->log('create', ProductSerial::class, $serial->id, $data);
        return redirect()->route('product-serials.index')->with('success', 'เพิ่ม Serial Number สำเร็จ');
    }

    public function edit(ProductSerial $productSerial)
    {
        $products = Product::where('requires_serial', true)->orderBy('name')->get();
        return view('product-serials.edit', compact('productSerial', 'products'));
    }

    public function update(Request $request, ProductSerial $productSerial)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'serial_no'  => 'required|string|unique:product_serials,serial_no,' . $productSerial->id,
            'status'     => 'required|in:available,rented,maintenance,lost',
            'note'       => 'nullable|string',
        ]);
        $productSerial->update($data);
        $this->auditLogService->log('update', ProductSerial::class, $productSerial->id, $data);
        return redirect()->route('product-serials.index')->with('success', 'อัปเดต Serial Number สำเร็จ');
    }

    public function history(ProductSerial $productSerial)
    {
        $history = RentalSerial::with(['rentalItem.rental.customer', 'rentalItem.rental.sale'])
            ->where('product_serial_id', $productSerial->id)
            ->orderByDesc('rented_at')
            ->paginate(20);
        return view('product-serials.history', compact('productSerial', 'history'));
    }
}
