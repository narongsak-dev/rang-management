@extends('layouts.admin')

@section('title', __('ประวัติ Serial'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('product-serials.index') }}">{{ __('Serial Number ปืน') }}</a></li>
    <li class="breadcrumb-item active">{{ __('ประวัติ') }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('ประวัติการเช่า') }}: <strong>{{ $productSerial->serial_no }}</strong>
            <span class="ms-2 text-muted small">({{ $productSerial->product->name ?? '' }})</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('วันที่เช่า') }}</th>
                        <th>{{ __('วันที่คืน') }}</th>
                        <th>{{ __('ลูกค้า') }}</th>
                        <th>{{ __('เลขออเดอร์') }}</th>
                        <th class="text-center">{{ __('สถานะ') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $rs)
                    <tr>
                        <td>{{ $rs->rented_at ? $rs->rented_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $rs->returned_at ? $rs->returned_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $rs->rentalItem->rental->customer->name ?? '-' }}</td>
                        <td>{{ $rs->rentalItem->rental->sale->order_no ?? '-' }}</td>
                        <td class="text-center">
                            @if($rs->returned_at)
                                <span class="badge bg-success">{{ __('คืนแล้ว') }}</span>
                            @else
                                <span class="badge bg-warning">{{ __('ยังไม่คืน') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('ไม่มีประวัติการเช่า') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $history->links() }}
        <a href="{{ route('product-serials.index') }}" class="btn btn-secondary mt-2">{{ __('กลับ') }}</a>
    </div>
</div>
@endsection
