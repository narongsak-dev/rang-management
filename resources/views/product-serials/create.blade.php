@extends('layouts.admin')

@section('title', __('เพิ่ม Serial Number'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('product-serials.index') }}">{{ __('Serial Number ปืน') }}</a></li>
    <li class="breadcrumb-item active">{{ __('เพิ่ม') }}</li>
@endsection

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header"><h5 class="mb-0">{{ __('เพิ่ม Serial Number') }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('product-serials.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('สินค้า') }} <span class="text-danger">*</span></label>
                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">{{ __('-- เลือกสินค้า --') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('หมายเลข Serial') }} <span class="text-danger">*</span></label>
                <input type="text" name="serial_no" value="{{ old('serial_no') }}" class="form-control @error('serial_no') is-invalid @enderror" required>
                @error('serial_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('สถานะ') }} <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>{{ __('ว่าง') }}</option>
                    <option value="rented" {{ old('status') === 'rented' ? 'selected' : '' }}>{{ __('กำลังเช่า') }}</option>
                    <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>{{ __('ซ่อมบำรุง') }}</option>
                    <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>{{ __('สูญหาย') }}</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('หมายเหตุ') }}</label>
                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="2">{{ old('note') }}</textarea>
                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('บันทึก') }}</button>
                <a href="{{ route('product-serials.index') }}" class="btn btn-secondary">{{ __('ยกเลิก') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
