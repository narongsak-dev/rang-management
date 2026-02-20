@extends('layouts.admin')

@section('title', 'Rental #' . $rental->id)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('rentals.index') }}">Rentals</a></li>
    <li class="breadcrumb-item active">#{{ $rental->id }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    @if($rental->status === 'open')
    <a href="{{ route('rentals.return', $rental) }}" class="btn btn-warning">
        <i class="bi bi-arrow-return-left"></i> Process Return
    </a>
    @else
    <span></span>
    @endif
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Rental Info</h6></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">Customer</dt>
                    <dd class="col-sm-9">{{ $rental->customer->name }}</dd>
                    <dt class="col-sm-3 text-muted">Order</dt>
                    <dd class="col-sm-9 font-monospace">{{ $rental->sale->order_no }}</dd>
                    <dt class="col-sm-3 text-muted">Rented At</dt>
                    <dd class="col-sm-9">{{ $rental->rented_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-3 text-muted">Status</dt>
                    <dd class="col-sm-9">
                        <span class="badge {{ $rental->status === 'open' ? 'bg-warning text-dark' : 'bg-success' }}">{{ $rental->status }}</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Rental Items</h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Deposit</th>
                                <th class="text-center">Returned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rental->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">฿{{ number_format($item->deposit_amount, 2) }}</td>
                                <td class="text-center">
                                    @if($item->returned_at)
                                    <span class="text-success small">{{ $item->returned_at->format('d/m/Y H:i') }}</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
