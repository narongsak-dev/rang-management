@extends('layouts.admin')

@section('title', 'Inventory Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Inventory</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Inventory Report</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Barcode</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th class="text-end">Stock Qty</th>
                        <th class="text-end">Available</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ !$product->is_active ? 'table-secondary opacity-50' : '' }}">
                        <td class="font-monospace">{{ $product->barcode }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->type }}</td>
                        <td class="text-end {{ $product->stock_qty <= 5 ? 'text-danger fw-bold' : '' }}">{{ $product->stock_qty }}</td>
                        <td class="text-end">{{ $product->available_qty }}</td>
                        <td class="text-end">฿{{ number_format($product->price, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No products.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
