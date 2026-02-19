@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Barcode *</label>
        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Name *</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Type *</label>
        <select name="type" class="mt-1 block w-full border rounded px-3 py-2" required>
            @foreach(['sale','rent','service','fee'] as $t)
            <option value="{{ $t }}" @selected(old('type', $product->type ?? '') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Price (฿) *</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Deposit (฿)</label>
        <input type="number" step="0.01" name="deposit" value="{{ old('deposit', $product->deposit ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2">
    </div>
    @if(!isset($product))
    <div>
        <label class="block text-sm font-medium text-gray-700">Stock Qty *</label>
        <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}" class="mt-1 block w-full border rounded px-3 py-2" required min="0">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Available Qty *</label>
        <input type="number" name="available_qty" value="{{ old('available_qty', 0) }}" class="mt-1 block w-full border rounded px-3 py-2" required min="0">
    </div>
    @endif
    <div class="flex items-center mt-4">
        <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active ?? true)) class="mr-2">
        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
    </div>
</div>
