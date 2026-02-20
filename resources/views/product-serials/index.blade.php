@extends('layouts.admin')

@section('title', __('Serial Number ปืน'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">{{ __('Serial Number ปืน') }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('รายการ Serial Number') }}</h5>
        <a href="{{ route('product-serials.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> {{ __('เพิ่ม Serial') }}
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ค้นหาหมายเลข Serial') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('-- ทุกสถานะ --') }}</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>{{ __('ว่าง') }}</option>
                    <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>{{ __('กำลังเช่า') }}</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>{{ __('ซ่อมบำรุง') }}</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>{{ __('สูญหาย') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="product_id" class="form-select">
                    <option value="">{{ __('-- ทุกสินค้า --') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">{{ __('ค้นหา') }}</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('หมายเลข Serial') }}</th>
                        <th>{{ __('สินค้า') }}</th>
                        <th class="text-center">{{ __('สถานะ') }}</th>
                        <th>{{ __('หมายเหตุ') }}</th>
                        <th class="text-center">{{ __('จัดการ') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serials as $serial)
                    <tr>
                        <td class="fw-medium">{{ $serial->serial_no }}</td>
                        <td>{{ $serial->product->name ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $badges = ['available'=>'bg-success','rented'=>'bg-warning','maintenance'=>'bg-secondary','lost'=>'bg-danger'];
                                $labels = ['available'=>'ว่าง','rented'=>'กำลังเช่า','maintenance'=>'ซ่อมบำรุง','lost'=>'สูญหาย'];
                            @endphp
                            <span class="badge {{ $badges[$serial->status] ?? 'bg-secondary' }}">{{ __($labels[$serial->status] ?? $serial->status) }}</span>
                        </td>
                        <td>{{ $serial->note ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('product-serials.history', $serial) }}" class="btn btn-sm btn-outline-info me-1" title="{{ __('ประวัติ') }}"><i class="bi bi-clock-history"></i></a>
                            <a href="{{ route('product-serials.edit', $serial) }}" class="btn btn-sm btn-outline-primary" title="{{ __('แก้ไข') }}"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('ไม่พบ Serial Number') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $serials->links() }}
    </div>
</div>
@endsection
