@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h5 class="mb-0">Edit Customer</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            @include('customers._form')
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
