<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private AuditLogService $auditLogService,
    ) {}

    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        Gate::authorize('create', Product::class);
        return view('products.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);
        $data = $request->validate([
            'barcode'      => 'required|string|unique:products',
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:sale,rent,service,fee',
            'price'        => 'required|numeric|min:0',
            'deposit'      => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
            'available_qty'=> 'required|integer|min:0',
            'is_active'    => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $product = Product::create($data);
        $this->auditLogService->log('create', Product::class, $product->id, $data);
        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        Gate::authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('update', $product);
        $data = $request->validate([
            'barcode'      => 'required|string|unique:products,barcode,' . $product->id,
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:sale,rent,service,fee',
            'price'        => 'required|numeric|min:0',
            'deposit'      => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);
        $this->auditLogService->log('update', Product::class, $product->id, $data);
        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function stockAdjust(Request $request, Product $product)
    {
        Gate::authorize('adjust', $product);
        $data = $request->validate([
            'new_qty' => 'required|integer|min:0',
            'note'    => 'nullable|string|max:255',
        ]);
        $this->inventoryService->adjust($product, $data['new_qty'], $data['note'] ?? '');
        return back()->with('success', 'Stock adjusted.');
    }

    public function stockIn(Request $request, Product $product)
    {
        Gate::authorize('adjust', $product);
        $data = $request->validate([
            'qty'  => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);
        $this->inventoryService->stockIn($product, $data['qty'], $data['note'] ?? '');
        return back()->with('success', 'Stock added.');
    }
}
