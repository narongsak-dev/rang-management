@extends('layouts.admin')

@section('title', 'Edit Product: ' . $product->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h5 class="mb-0">Edit Product: {{ $product->name }}</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')
            @include('products._form')
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        @can('adjust', $product)
        <div class="mt-4 pt-4 border-top">
            <h6 class="fw-semibold mb-3">Stock Management</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="POST" action="{{ route('products.stock-in', $product) }}">
                        @csrf
                        <label class="form-label small fw-medium">Stock In</label>
                        <div class="input-group">
                            <input type="number" name="qty" min="1" placeholder="Qty" class="form-control" style="max-width:80px;">
                            <input type="text" name="note" placeholder="Note" class="form-control">
                            <button type="submit" class="btn btn-success">+In</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="POST" action="{{ route('products.adjust', $product) }}">
                        @csrf
                        <label class="form-label small fw-medium">Adjust Stock (current: {{ $product->stock_qty }})</label>
                        <div class="input-group">
                            <input type="number" name="new_qty" min="0" value="{{ $product->stock_qty }}" class="form-control" style="max-width:80px;">
                            <input type="text" name="note" placeholder="Reason" class="form-control">
                            <button type="submit" class="btn btn-warning">Adjust</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan

        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-semibold mb-2">Recent Movements</h6>
            @foreach($product->inventoryMovements()->with('creator')->latest()->limit(5)->get() as $m)
            <div class="d-flex justify-content-between text-sm py-1 border-bottom">
                <span class="fw-medium {{ $m->type==='in' ? 'text-success' : ($m->type==='out' ? 'text-danger' : 'text-warning') }}">
                    {{ strtoupper($m->type) }} {{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}
                </span>
                <span class="text-muted">{{ $m->note }}</span>
                <span class="text-muted small">{{ $m->creator->name }} · {{ $m->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
