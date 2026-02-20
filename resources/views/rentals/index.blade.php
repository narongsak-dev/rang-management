@extends('layouts.admin')

@section('title', 'Rentals')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Rentals</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Rentals</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-sm-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="open" @selected(request('status')==='open')>Open</option>
                    <option value="returned" @selected(request('status')==='returned')>Returned</option>
                </select>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Rental ID</th>
                        <th>Customer</th>
                        <th>Order</th>
                        <th>Rented At</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                    <tr>
                        <td class="font-monospace">#{{ $rental->id }}</td>
                        <td>{{ $rental->customer->name }}</td>
                        <td class="font-monospace">{{ $rental->sale->order_no }}</td>
                        <td>{{ $rental->rented_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $rental->status === 'open' ? 'bg-warning text-dark' : 'bg-success' }}">{{ $rental->status }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-sm btn-outline-info">View</a>
                            @if($rental->status === 'open')
                            <a href="{{ route('rentals.return', $rental) }}" class="btn btn-sm btn-outline-warning">Return</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No rentals found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rentals->links() }}</div>
    </div>
</div>
@endsection
