@extends('layouts.admin')

@section('title', __('จัดการ Permission'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('จัดการ Role') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Permission') }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('รายการ Permission') }}</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ชื่อ Permission') }}</th>
                                <th class="text-center">{{ __('จัดการ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('permissions.destroy', $permission) }}" onsubmit="return confirm('{{ __('ยืนยันการลบ?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">{{ __('ไม่มี Permission') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('เพิ่ม Permission') }}</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('permissions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('ชื่อ Permission') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('เช่น sale.create, product.view') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('เพิ่ม') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
