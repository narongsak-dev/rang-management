@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Citizen ID *</label>
        <input type="text" name="citizen_id" value="{{ old('citizen_id', $customer->citizen_id ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Name *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2">
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" rows="2" class="mt-1 block w-full border rounded px-3 py-2">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
</div>
