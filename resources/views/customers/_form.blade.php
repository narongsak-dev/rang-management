@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
<div class="row g-3">
    <div class="col-sm-6">
        <label class="form-label">Citizen ID *</label>
        <input type="text" name="citizen_id" value="{{ old('citizen_id', $customer->citizen_id ?? '') }}" class="form-control" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Name *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Address</label>
        <textarea name="address" rows="2" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
</div>
