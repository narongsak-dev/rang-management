@extends('layouts.admin')

@section('title', 'Customers')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customers</h5>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Customer
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-sm-8">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/citizen ID/phone..." class="form-control">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Citizen ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th class="text-center">Memberships</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="font-monospace">{{ $customer->citizen_id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td class="text-center">{{ $customer->memberships_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
