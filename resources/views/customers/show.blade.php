@extends('layouts.admin')

@section('title', 'Customer: ' . $customer->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">{{ $customer->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-end">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Customer Info</h6></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Citizen ID</dt>
                    <dd class="col-sm-8 font-monospace">{{ $customer->citizen_id }}</dd>
                    <dt class="col-sm-4 text-muted">Phone</dt>
                    <dd class="col-sm-8">{{ $customer->phone ?? '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Address</dt>
                    <dd class="col-sm-8">{{ $customer->address ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Memberships</h6>
                <a href="{{ route('memberships.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-success">
                    + New Membership
                </a>
            </div>
            <div class="card-body">
                @forelse($customer->memberships as $m)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <span class="font-monospace fw-medium">{{ $m->member_no }}</span>
                        <span class="text-muted small ms-2">{{ $m->started_at->format('d/m/Y') }} – {{ $m->expires_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $m->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ $m->status }}</span>
                        <form method="POST" action="{{ route('memberships.renew', $m) }}">@csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">Renew</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-muted small mb-0">No memberships yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Recent Sales</h6></div>
            <div class="card-body">
                @forelse($customer->sales as $sale)
                <div class="d-flex justify-content-between py-2 border-bottom small">
                    <a href="{{ route('pos.receipt', $sale) }}" class="font-monospace text-primary">{{ $sale->order_no }}</a>
                    <span>฿{{ number_format($sale->total, 2) }}</span>
                    <span class="text-muted">{{ $sale->paid_at?->format('d/m/Y H:i') }}</span>
                </div>
                @empty
                <p class="text-muted small mb-0">No sales yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
