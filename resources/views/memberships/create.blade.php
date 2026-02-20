@extends('layouts.admin')

@section('title', 'New Membership')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('memberships.index') }}">Memberships</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="card" style="max-width: 540px;">
    <div class="card-header">
        <h5 class="mb-0">New Membership</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('memberships.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Customer *</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" @selected(old('customer_id', request('customer_id')) == $c->id)>{{ $c->name }} ({{ $c->citizen_id }})</option>
                    @endforeach
                </select>
            </div>
            <p class="text-muted small mb-4">New membership will be valid for 1 year from today.</p>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Membership</button>
                <a href="{{ route('memberships.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
