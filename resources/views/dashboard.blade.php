@extends('layouts.admin')

@section('title', __('แดชบอร์ด'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">{{ __('แดชบอร์ด') }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-secondary fs-1"><i class="bi bi-boxes"></i></div>
                <div>
                    <div class="text-muted small">{{ __('สินค้าทั้งหมด') }}</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-success fs-1"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted small">{{ __('ยอดขายวันนี้') }}</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['today_sales'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-success fs-1"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted small">{{ __('รายได้วันนี้') }}</div>
                    <div class="fs-3 fw-bold text-success">฿{{ number_format($stats['today_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-warning fs-1"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="text-muted small">{{ __('เช่าที่ยังไม่คืน') }}</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['open_rentals'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-primary fs-1"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-muted small">{{ __('ลูกค้าทั้งหมด') }}</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_customers'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-danger fs-1"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">{{ __('สินค้าใกล้หมด') }}</div>
                    <div class="fs-3 fw-bold text-danger">{{ $stats['low_stock'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    @hasrole('cashier|admin')
    <div class="col-md-6">
        <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg w-100 py-4 fs-5 fw-bold">
            🛒 {{ __('ไปที่ POS') }}
        </a>
    </div>
    @endhasrole
    @hasrole('inventory|admin')
    <div class="col-md-6">
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg w-100 py-4 fs-5 fw-bold">
            📦 {{ __('จัดการสินค้า') }}
        </a>
    </div>
    @endhasrole
</div>

<!-- Charts -->
<div class="row g-4 mt-2">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('ยอดขาย 7 วันล่าสุด') }}</h5>
            </div>
            <div class="card-body">
                <canvas id="sales7DaysChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('ยอดขายรายประเภท (6 เดือนล่าสุด)') }}</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyTypeChart" height="160"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("dashboard.stats") }}', {
        headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        // Sales 7 days
        const s7 = data.sales_7days || [];
        new Chart(document.getElementById('sales7DaysChart'), {
            type: 'bar',
            data: {
                labels: s7.map(d => d.label),
                datasets: [{
                    label: 'รายได้ (฿)',
                    data: s7.map(d => d.revenue),
                    backgroundColor: 'rgba(13,110,253,0.7)',
                    borderColor: 'rgba(13,110,253,1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Monthly by type
        const mt = data.monthly_by_type || [];
        new Chart(document.getElementById('monthlyTypeChart'), {
            type: 'bar',
            data: {
                labels: mt.map(d => d.label),
                datasets: [
                    {
                        label: 'ขาย',
                        data: mt.map(d => d.sale),
                        backgroundColor: 'rgba(25,135,84,0.7)',
                    },
                    {
                        label: 'เช่า',
                        data: mt.map(d => d.rent),
                        backgroundColor: 'rgba(255,193,7,0.7)',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { x: { stacked: false }, y: { beginAtZero: true } }
            }
        });
    })
    .catch(console.error);
});
</script>
@endpush
