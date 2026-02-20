@extends('layouts.admin')

@section('title', 'Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Profile Information</h6></div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Update Password</h6></div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <div class="col-md-8 col-lg-6">
        <div class="card border-danger">
            <div class="card-header text-danger"><h6 class="mb-0">Delete Account</h6></div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
