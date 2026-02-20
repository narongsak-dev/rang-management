@extends('layouts.admin')

@section('title', 'Memberships')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Memberships</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Memberships</h5>
        <a href="{{ route('memberships.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> New Membership
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-sm-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search member no / customer..." class="form-control">
            </div>
            <div class="col-sm-3">
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="expired" @selected(request('status')==='expired')>Expired</option>
                </select>
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member No</th>
                        <th>Customer</th>
                        <th>Started</th>
                        <th>Expires</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $m)
                    <tr>
                        <td class="font-monospace">{{ $m->member_no }}</td>
                        <td><a href="{{ route('customers.show', $m->customer) }}" class="text-primary">{{ $m->customer->name }}</a></td>
                        <td>{{ $m->started_at->format('d/m/Y') }}</td>
                        <td>{{ $m->expires_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $m->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ $m->status }}</span>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('memberships.renew', $m) }}" class="d-inline">@csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">Renew</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No memberships found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $memberships->links() }}</div>
    </div>
</div>
@endsection
