@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-secondary fs-1"><i class="bi bi-boxes"></i></div>
                <div>
                    <div class="text-muted small">Total Products</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-success fs-1"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted small">Today Sales</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['today_sales'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-success fs-1"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted small">Today Revenue</div>
                    <div class="fs-3 fw-bold text-success">฿{{ number_format($stats['today_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-warning fs-1"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="text-muted small">Open Rentals</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['open_rentals'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-primary fs-1"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-muted small">Total Customers</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_customers'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-secondary fs-1"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Active Products</div>
                    <div class="fs-3 fw-bold">{{ $stats['active_products'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    @hasrole('cashier|admin')
    <div class="col-md-6">
        <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg w-100 py-4 fs-5 fw-bold">
            🛒 Go to POS
        </a>
    </div>
    @endhasrole
    @hasrole('inventory|admin')
    <div class="col-md-6">
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg w-100 py-4 fs-5 fw-bold">
            📦 Inventory
        </a>
    </div>
    @endhasrole
</div>
@endsection
