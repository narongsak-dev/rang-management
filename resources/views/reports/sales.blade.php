@extends('layouts.admin')

@section('title', 'Sales Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Sales</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Sales Report</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-sm-4">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-sm-4">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>
        <div class="alert alert-info mb-3">
            Total: <strong>฿{{ number_format($total, 2) }}</strong>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th class="text-end">Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td><a href="{{ route('pos.receipt', $sale) }}" class="font-monospace text-primary">{{ $sale->order_no }}</a></td>
                        <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->staff->name }}</td>
                        <td class="text-end">฿{{ number_format($sale->total, 2) }}</td>
                        <td>{{ strtoupper($sale->payment_method) }}</td>
                        <td>{{ $sale->paid_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $sales->links() }}</div>
    </div>
</div>
@endsection
