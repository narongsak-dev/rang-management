@extends('layouts.admin')

@section('title', __('จัดการ Role'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">{{ __('จัดการ Role') }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('รายการ Role') }}</h5>
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> {{ __('สร้าง Role') }}
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ชื่อ Role') }}</th>
                                <th class="text-center">{{ __('จำนวน Permission') }}</th>
                                <th class="text-center">{{ __('จัดการ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td class="fw-medium">{{ $role->name }}</td>
                                <td class="text-center">{{ $role->permissions_count }}</td>
                                <td class="text-center">
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>{{ __('แก้ไข') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">{{ __('ไม่มี Role') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Permission') }}</h5>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">{{ __('จัดการ Permission') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
