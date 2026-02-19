@extends('layouts.admin')

@section('title', 'Reports')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <a href="{{ route('reports.sales') }}" class="card text-decoration-none text-center h-100 shadow-sm hover-shadow">
            <div class="card-body py-5">
                <div class="fs-1 mb-3">💰</div>
                <h5 class="fw-bold">Sales Report</h5>
                <p class="text-muted small mb-0">View sales history and revenue</p>
            </div>
        </a>
    </div>
    @can('update', App\Models\Product::class)
    <div class="col-md-6">
        <a href="{{ route('reports.inventory') }}" class="card text-decoration-none text-center h-100 shadow-sm">
            <div class="card-body py-5">
                <div class="fs-1 mb-3">📦</div>
                <h5 class="fw-bold">Inventory Report</h5>
                <p class="text-muted small mb-0">View stock levels and movements</p>
            </div>
        </a>
    </div>
    @endcan
</div>
@endsection
