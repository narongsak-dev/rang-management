@extends('layouts.admin')

@section('title', 'Add Product')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Add Product</li>
@endsection

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h5 class="mb-0">Add Product</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            @include('products._form')
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
