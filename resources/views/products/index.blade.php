@extends('layouts.admin')

@section('title', 'Products')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products</h5>
        @can('create', App\Models\Product::class)
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
        @endcan
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-sm-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/barcode..." class="form-control">
            </div>
            <div class="col-sm-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['sale','rent','service','fee'] as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Barcode</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Stock</th>
                        <th class="text-end">Available</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="font-monospace">{{ $product->barcode }}</td>
                        <td>{{ $product->name }}</td>
                        <td>
                            <span class="badge {{ match($product->type) { 'sale'=>'bg-primary', 'rent'=>'bg-success', 'service'=>'bg-warning text-dark', default=>'bg-secondary' } }}">
                                {{ $product->type }}
                            </span>
                        </td>
                        <td class="text-end">฿{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">{{ $product->stock_qty }}</td>
                        <td class="text-end">{{ $product->available_qty }}</td>
                        <td class="text-center">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @can('update', $product)
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
