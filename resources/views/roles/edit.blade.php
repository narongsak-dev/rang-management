@extends('layouts.admin')

@section('title', __('แก้ไข Role'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('จัดการ Role') }}</a></li>
    <li class="breadcrumb-item active">{{ __('แก้ไข') }}</li>
@endsection

@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5 class="mb-0">{{ __('แก้ไข Role') }}: {{ $role->name }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ __('ชื่อ Role') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if($permissions->count())
            <div class="mb-3">
                <label class="form-label fw-medium">{{ __('Permission') }}</label>
                @foreach($permissions as $group => $perms)
                <div class="mb-2">
                    <div class="text-muted small fw-medium mb-1">{{ $group }}</div>
                    <div class="row g-1">
                        @foreach($perms as $permission)
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('บันทึก') }}</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('ยกเลิก') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
