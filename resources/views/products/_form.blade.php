@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
<div class="row g-3">
    <div class="col-sm-6">
        <label class="form-label">Barcode *</label>
        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="form-control" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Name *</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Type *</label>
        <select name="type" class="form-select" required>
            @foreach(['sale','rent','service','fee'] as $t)
            <option value="{{ $t }}" @selected(old('type', $product->type ?? '') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Price (฿) *</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" class="form-control" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Deposit (฿)</label>
        <input type="number" step="0.01" name="deposit" value="{{ old('deposit', $product->deposit ?? '') }}" class="form-control">
    </div>
    @if(!isset($product))
    <div class="col-sm-6">
        <label class="form-label">Stock Qty *</label>
        <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}" class="form-control" required min="0">
    </div>
    <div class="col-sm-6">
        <label class="form-label">Available Qty *</label>
        <input type="number" name="available_qty" value="{{ old('available_qty', 0) }}" class="form-control" required min="0">
    </div>
    @endif
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   @checked(old('is_active', $product->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>
