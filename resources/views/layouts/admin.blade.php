<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ระบบจัดการสนามยิงปืน')) | {{ config('app.name', 'ระบบจัดการสนามยิงปืน') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE 4 (Bootstrap 5 based) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list fs-5"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="bi bi-house"></i> หน้าหลัก
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text text-muted small">
                                {{ Auth::user()->email }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-1"></i> โปรไฟล์
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar bg-dark sidebar-dark-primary shadow" data-bs-theme="dark">

        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
                <span class="brand-text fw-bold fs-5">🎯 สนามยิงปืน</span>
            </a>
        </div>

        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>{{ __('แดชบอร์ด') }}</p>
                        </a>
                    </li>

                    @hasrole('cashier|admin')
                    <li class="nav-item">
                        <a href="{{ route('pos.index') }}"
                           class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-cart3"></i>
                            <p>{{ __('จุดขาย (POS)') }}</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}"
                           class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>{{ __('ลูกค้า') }}</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('memberships.index') }}"
                           class="nav-link {{ request()->routeIs('memberships.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-card-checklist"></i>
                            <p>{{ __('สมาชิก') }}</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('rentals.index') }}"
                           class="nav-link {{ request()->routeIs('rentals.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-box-seam"></i>
                            <p>{{ __('การเช่า') }}</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}"
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-bar-chart"></i>
                            <p>{{ __('รายงาน') }}</p>
                        </a>
                    </li>
                    @endhasrole

                    <li class="nav-item">
                        <a href="{{ route('products.index') }}"
                           class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-boxes"></i>
                            <p>{{ __('สินค้า') }}</p>
                        </a>
                    </li>

                    @hasrole('inventory|admin')
                    <li class="nav-item">
                        <a href="{{ route('product-serials.index') }}"
                           class="nav-link {{ request()->routeIs('product-serials.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-upc-scan"></i>
                            <p>{{ __('Serial Number ปืน') }}</p>
                        </a>
                    </li>
                    @endhasrole

                    @hasrole('admin')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}"
                           class="nav-link {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-shield-lock"></i>
                            <p>{{ __('จัดการสิทธิ์') }}</p>
                        </a>
                    </li>
                    @endhasrole

                </ul>
            </nav>
        </div>
    </aside>

    <main class="app-main">

        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('title', 'แดชบอร์ด')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('หน้าหลัก') }}</a>
                            </li>
                            @yield('breadcrumbs')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')

            </div>
        </div>
    </main>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 4 JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>
