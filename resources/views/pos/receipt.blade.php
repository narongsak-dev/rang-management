@extends('layouts.admin')

@section('title', 'Receipt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">POS</a></li>
    <li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')
<div class="d-flex justify-content-end gap-2 mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Print
    </button>
    <a href="{{ route('pos.index') }}" class="btn btn-success">
        <i class="bi bi-cart-plus"></i> New Sale
    </a>
</div>

<div class="card mx-auto" style="max-width: 480px;" id="receipt">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <h4 class="fw-bold">🎯 GunRange Management</h4>
            <p class="text-muted small mb-0">Official Receipt</p>
        </div>
        <table class="table table-borderless table-sm mb-0">
            <tr>
                <td class="fw-medium">Receipt No:</td>
                <td class="text-end font-monospace">{{ $sale->receipt?->receipt_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Order No:</td>
                <td class="text-end font-monospace">{{ $sale->order_no }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Date:</td>
                <td class="text-end">{{ $sale->paid_at?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Staff:</td>
                <td class="text-end">{{ $sale->staff->name }}</td>
            </tr>
            @if($sale->customer)
            <tr>
                <td class="fw-medium">Customer:</td>
                <td class="text-end">{{ $sale->customer->name }}</td>
            </tr>
            @endif
        </table>

        <hr>

        <table class="table table-sm mb-0">
            <thead>
                <tr class="text-muted">
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}{{ $item->is_rental ? ' [RENT]' : '' }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">฿{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">฿{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <table class="table table-borderless table-sm mb-0">
            <tr>
                <td>Subtotal</td>
                <td class="text-end">฿{{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr class="text-danger">
                <td>Discount</td>
                <td class="text-end">-฿{{ number_format($sale->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="fw-bold fs-5 border-top">
                <td>TOTAL</td>
                <td class="text-end">฿{{ number_format($sale->total, 2) }}</td>
            </tr>
            <tr class="text-muted">
                <td>Payment</td>
                <td class="text-end">{{ strtoupper($sale->payment_method) }}</td>
            </tr>
        </table>

        <div class="text-center mt-4 text-muted small">Thank you for visiting GunRange!</div>
    </div>
</div>
@endsection
