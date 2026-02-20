@extends('layouts.admin')

@section('title', 'Process Return — Rental #' . $rental->id)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('rentals.index') }}">Rentals</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rentals.show', $rental) }}">#{{ $rental->id }}</a></li>
    <li class="breadcrumb-item active">Return</li>
@endsection

@section('content')
<div class="card" style="max-width: 640px;">
    <div class="card-header">
        <h5 class="mb-0">Process Return — Rental #{{ $rental->id }}</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <p class="text-muted mb-4">Customer: <strong>{{ $rental->customer->name }}</strong></p>
        <form method="POST" action="{{ route('rentals.process-return', $rental) }}">
            @csrf
            <h6 class="fw-medium mb-3">Select items to return:</h6>
            @forelse($pendingItems as $item)
            <label class="d-flex align-items-center gap-3 p-3 border rounded mb-2 cursor-pointer">
                <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" checked class="form-check-input mt-0">
                <div class="flex-fill">
                    <span class="fw-medium">{{ $item->product->name }}</span>
                    <span class="text-muted small ms-2">Qty: {{ $item->qty }}</span>
                    <span class="text-muted small ms-2">Deposit: ฿{{ number_format($item->deposit_amount, 2) }}</span>
                </div>
            </label>
            @empty
            <p class="text-muted">All items already returned.</p>
            @endforelse
            @if($pendingItems->isNotEmpty())
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-warning">Process Return</button>
                <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary">Cancel</a>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
