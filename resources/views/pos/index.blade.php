@extends('layouts.admin')

@section('title', __('จุดขาย (POS)'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">{{ __('จุดขาย (POS)') }}</li>
@endsection

@section('content')
<div class="row g-3">
    <!-- ตะกร้าสินค้า -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('ตะกร้าสินค้า') }}</h5>
                <small class="text-muted">F2 = บัตรประชาชน &nbsp;|&nbsp; F3 = บาร์โค้ด</small>
            </div>
            <div class="card-body">
                <!-- ช่องเลขบัตรประชาชน -->
                <div class="mb-3 p-3 bg-light rounded border">
                    <label class="form-label fw-medium">{{ __('เลขบัตรประชาชน') }} <kbd>F2</kbd></label>
                    <div class="input-group">
                        <input type="text" id="citizen-id-input" placeholder="{{ __('สแกนหรือพิมพ์เลขบัตรประชาชน...') }}" class="form-control" maxlength="13" inputmode="numeric">
                        <button onclick="searchCustomer()" class="btn btn-primary">{{ __('ค้นหา') }}</button>
                        <button onclick="clearCustomer()" class="btn btn-secondary">{{ __('ล้าง') }}</button>
                    </div>
                    <div id="customer-info" class="mt-2 d-none">
                        <span class="fw-medium text-success" id="customer-name"></span>
                        <span class="ms-2 badge" id="membership-badge"></span>
                    </div>
                    <div id="customer-not-found" class="mt-2 d-none">
                        <span class="text-warning">{{ __('ไม่พบลูกค้า') }}</span>
                        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-outline-primary ms-2">{{ __('ลงทะเบียนลูกค้า') }}</a>
                    </div>
                </div>
                <!-- ช่องบาร์โค้ด -->
                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('บาร์โค้ดสินค้า') }} <kbd>F3</kbd></label>
                    <div class="input-group">
                        <input type="text" id="barcode-input" placeholder="{{ __('สแกนหรือพิมพ์บาร์โค้ด...') }}" class="form-control">
                        <select id="unit-type-select" class="form-select" style="max-width:130px;">
                            <option value="unit">{{ __('ต่อชิ้น') }}</option>
                            <option value="box">{{ __('ยกกล่อง') }}</option>
                        </select>
                        <select id="mode-select" class="form-select" style="max-width:120px;">
                            <option value="sale">{{ __('ขาย') }}</option>
                            <option value="rent">{{ __('เช่า') }}</option>
                        </select>
                        <button onclick="addItem()" class="btn btn-primary">{{ __('เพิ่ม') }}</button>
                    </div>
                    <div id="scan-error" class="text-danger small mt-1 d-none"></div>
                </div>
                <!-- ตาราง -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('สินค้า') }}</th>
                                <th class="text-center">{{ __('ประเภท') }}</th>
                                <th class="text-center">{{ __('จำนวน') }}</th>
                                <th class="text-end">{{ __('ราคา') }}</th>
                                <th class="text-end">{{ __('รวม') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <tr id="empty-row"><td colspan="6" class="text-center text-muted py-4">{{ __('ไม่มีสินค้าในตะกร้า') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- แผงชำระเงิน -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('ชำระเงิน') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <span>{{ __('ยอดรวม') }}</span>
                    <span id="subtotal">฿0.00</span>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span>{{ __('ส่วนลด') }}</span>
                    <input type="number" id="discount" value="0" min="0" step="0.01" class="form-control w-auto text-end" style="max-width:120px;" oninput="updateTotals()">
                </div>
                <div class="mb-3 d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                    <span>{{ __('ยอดสุทธิ') }}</span>
                    <span id="total">฿0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('วิธีชำระเงิน') }}</label>
                    <select id="payment-method" class="form-select">
                        <option value="cash">{{ __('เงินสด') }}</option>
                        <option value="card">{{ __('บัตรเครดิต/เดบิต') }}</option>
                        <option value="transfer">{{ __('โอนเงิน') }}</option>
                    </select>
                </div>
                <button onclick="checkout()" id="checkout-btn" class="btn btn-success w-100 py-3 fw-bold fs-5" disabled>{{ __('ชำระเงิน') }}</button>
                <div id="checkout-error" class="text-danger small mt-2 d-none"></div>
            </div>
        </div>
    </div>
</div>

<!-- Serial Number Modal -->
<div class="modal fade" id="serialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('ระบุหมายเลข Serial') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2" id="serial-instructions"></p>
                <div id="serial-inputs"></div>
                <div class="mt-2">
                    <input type="text" id="serial-scan-input" class="form-control" placeholder="{{ __('สแกน Serial...') }}">
                </div>
                <div id="serial-error" class="text-danger small mt-2 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ยกเลิก') }}</button>
                <button type="button" class="btn btn-primary" onclick="confirmSerials()">{{ __('ยืนยัน') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];
let customer = null;
let currentSerialLineId = null;
let currentSerials = [];
let currentSerialQty = 0;

const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// F2/F3 shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('citizen-id-input').focus(); }
    if (e.key === 'F3') { e.preventDefault(); document.getElementById('barcode-input').focus(); }
});

document.getElementById('citizen-id-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchCustomer(); }
});
document.getElementById('barcode-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); addItem(); }
});

async function searchCustomer() {
    const citizenId = document.getElementById('citizen-id-input').value.trim();
    if (citizenId.length !== 13 || !/^\d+$/.test(citizenId)) {
        showScanError('{{ __("เลขบัตรประชาชนต้องมี 13 หลัก") }}', 'citizen');
        return;
    }
    try {
        const res = await fetch('{{ route("pos.customer.set") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
            body: JSON.stringify({citizen_id: citizenId})
        });
        const data = await res.json();
        const infoDiv = document.getElementById('customer-info');
        const notFound = document.getElementById('customer-not-found');
        if (data.found) {
            customer = data.customer;
            infoDiv.classList.remove('d-none');
            notFound.classList.add('d-none');
            document.getElementById('customer-name').textContent = customer.name + ' (' + customer.citizen_id + ')';
            const badge = document.getElementById('membership-badge');
            if (customer.active_membership) {
                badge.textContent = '{{ __("สมาชิก") }}';
                badge.className = 'ms-2 badge bg-success';
            } else {
                badge.textContent = '{{ __("ไม่เป็นสมาชิก") }}';
                badge.className = 'ms-2 badge bg-secondary';
            }
        } else {
            customer = null;
            infoDiv.classList.add('d-none');
            notFound.classList.remove('d-none');
        }
    } catch(e) { showScanError('{{ __("เกิดข้อผิดพลาด") }}', 'citizen'); }
}

async function clearCustomer() {
    await fetch('{{ route("pos.customer.clear") }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    });
    customer = null;
    document.getElementById('citizen-id-input').value = '';
    document.getElementById('customer-info').classList.add('d-none');
    document.getElementById('customer-not-found').classList.add('d-none');
}

async function addItem() {
    const barcode = document.getElementById('barcode-input').value.trim();
    if (!barcode) return;
    hideScanError();
    const unitType = document.getElementById('unit-type-select').value;
    const mode = document.getElementById('mode-select').value;
    try {
        const res = await fetch('{{ route("pos.cart.add") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
            body: JSON.stringify({barcode, qty: 1, unit_type: unitType, mode})
        });
        const data = await res.json();
        if (!data.success) { showScanError(data.message || '{{ __("เกิดข้อผิดพลาด") }}'); return; }
        cart = data.cart;
        document.getElementById('barcode-input').value = '';
        renderCart();
    } catch(e) { showScanError('{{ __("เกิดข้อผิดพลาดในการเชื่อมต่อ") }}'); }
}

function showScanError(msg, type) {
    const el = document.getElementById('scan-error');
    el.textContent = msg;
    el.classList.remove('d-none');
}
function hideScanError() {
    document.getElementById('scan-error').classList.add('d-none');
}

async function removeItem(lineId) {
    const res = await fetch('{{ route("pos.cart.remove") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
        body: JSON.stringify({line_id: lineId})
    });
    const data = await res.json();
    cart = data.cart;
    renderCart();
}

async function updateQty(lineId, qty) {
    const res = await fetch('{{ route("pos.cart.update") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
        body: JSON.stringify({line_id: lineId, qty: parseInt(qty)})
    });
    const data = await res.json();
    cart = data.cart;
    renderCart();
}

function openSerialModal(lineId, productName, qty) {
    currentSerialLineId = lineId;
    currentSerials = [];
    currentSerialQty = qty;
    const item = cart.find(i => i.id === lineId);
    currentSerials = item && item.serials ? [...item.serials] : [];
    document.getElementById('serial-instructions').textContent = `กรุณาระบุ Serial Number ของ "${productName}" จำนวน ${qty} หมายเลข`;
    document.getElementById('serial-error').classList.add('d-none');
    renderSerialInputs();
    document.getElementById('serial-scan-input').value = '';
    new bootstrap.Modal(document.getElementById('serialModal')).show();
    document.getElementById('serial-scan-input').focus();

    document.getElementById('serial-scan-input').onkeydown = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const sn = this.value.trim();
            if (!sn) return;
            if (currentSerials.includes(sn)) {
                document.getElementById('serial-error').textContent = '{{ __("Serial ซ้ำกัน") }}';
                document.getElementById('serial-error').classList.remove('d-none');
                return;
            }
            currentSerials.push(sn);
            this.value = '';
            document.getElementById('serial-error').classList.add('d-none');
            renderSerialInputs();
        }
    };
}

function renderSerialInputs() {
    const container = document.getElementById('serial-inputs');
    container.innerHTML = currentSerials.map((sn, i) =>
        `<div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary">${i+1}</span>
            <span class="flex-grow-1">${sn}</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSerial(${i})">✕</button>
        </div>`
    ).join('');
}

function removeSerial(idx) {
    currentSerials.splice(idx, 1);
    renderSerialInputs();
}

async function confirmSerials() {
    if (currentSerials.length !== currentSerialQty) {
        document.getElementById('serial-error').textContent = `{{ __("จำนวน Serial ไม่ครบ") }} (${currentSerials.length}/${currentSerialQty})`;
        document.getElementById('serial-error').classList.remove('d-none');
        return;
    }
    const res = await fetch('{{ route("pos.cart.serials") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
        body: JSON.stringify({line_id: currentSerialLineId, serials: currentSerials})
    });
    const data = await res.json();
    if (!data.success) {
        document.getElementById('serial-error').textContent = data.message;
        document.getElementById('serial-error').classList.remove('d-none');
        return;
    }
    cart = data.cart;
    bootstrap.Modal.getInstance(document.getElementById('serialModal')).hide();
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('cart-body');
    if (!cart || cart.length === 0) {
        tbody.innerHTML = '<tr id="empty-row"><td colspan="6" class="text-center text-muted py-4">{{ __("ไม่มีสินค้าในตะกร้า") }}</td></tr>';
        document.getElementById('checkout-btn').disabled = true;
        updateTotals();
        return;
    }
    tbody.innerHTML = cart.map(item => {
        const typeLabel = item.is_rental ? '{{ __("เช่า") }}' : (item.unit_type === 'box' ? '{{ __("ยกกล่อง") }}' : '{{ __("ขาย") }}');
        const typeBadge = item.is_rental ? 'bg-success' : (item.unit_type === 'box' ? 'bg-info' : 'bg-primary');
        const serialBtn = (item.is_rental && item.requires_serial) ?
            `<button onclick="openSerialModal('${item.id}','${item.product_name.replace(/'/g,"\\'")}',${item.qty})" class="btn btn-xs btn-outline-warning ms-1" title="{{ __('ระบุ Serial') }}">
                <i class="bi bi-upc-scan"></i>${item.serials && item.serials.length === item.qty ? ' ✓' : ''}
            </button>` : '';
        return `<tr>
            <td>${item.product_name}${serialBtn}</td>
            <td class="text-center"><span class="badge ${typeBadge}">${typeLabel}</span></td>
            <td class="text-center"><input type="number" min="1" value="${item.qty}" onchange="updateQty('${item.id}', this.value)" class="form-control form-control-sm text-center" style="width:70px;margin:auto;"></td>
            <td class="text-end">฿${parseFloat(item.unit_price).toFixed(2)}</td>
            <td class="text-end fw-medium">฿${parseFloat(item.line_total).toFixed(2)}</td>
            <td class="text-center"><button onclick="removeItem('${item.id}')" class="btn btn-sm btn-outline-danger">✕</button></td>
        </tr>`;
    }).join('');
    document.getElementById('checkout-btn').disabled = false;
    updateTotals();
}

function updateTotals() {
    if (!cart) cart = [];
    const subtotal = cart.reduce((s, i) => s + parseFloat(i.line_total), 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = Math.max(0, subtotal - discount);
    document.getElementById('subtotal').textContent = '฿' + subtotal.toFixed(2);
    document.getElementById('total').textContent = '฿' + total.toFixed(2);
}

async function checkout() {
    document.getElementById('checkout-error').classList.add('d-none');
    const hasRental = cart.some(i => i.is_rental);
    if (hasRental && !customer) {
        document.getElementById('checkout-error').textContent = '{{ __("กรุณาระบุลูกค้าก่อนทำรายการเช่า") }}';
        document.getElementById('checkout-error').classList.remove('d-none');
        return;
    }
    // Validate serials
    for (const item of cart) {
        if (item.is_rental && item.requires_serial) {
            if (!item.serials || item.serials.length !== item.qty) {
                document.getElementById('checkout-error').textContent = '{{ __("กรุณาระบุ Serial Number ให้ครบก่อนชำระเงิน") }}';
                document.getElementById('checkout-error').classList.remove('d-none');
                return;
            }
        }
    }
    const payload = {
        payment_method: document.getElementById('payment-method').value,
        discount: parseFloat(document.getElementById('discount').value) || 0,
        customer_id: customer ? customer.id : null,
    };
    try {
        const res = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) { window.location.href = data.receipt_url; }
        else {
            document.getElementById('checkout-error').textContent = data.error || '{{ __("เกิดข้อผิดพลาด") }}';
            document.getElementById('checkout-error').classList.remove('d-none');
        }
    } catch(e) {
        document.getElementById('checkout-error').textContent = '{{ __("เกิดข้อผิดพลาดในการเชื่อมต่อ") }}';
        document.getElementById('checkout-error').classList.remove('d-none');
    }
}

// Load cart on page load
fetch('{{ route("pos.cart") }}', {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}})
    .then(r => r.json())
    .then(data => {
        cart = data.cart || [];
        if (data.customer) {
            customer = data.customer;
            document.getElementById('customer-info').classList.remove('d-none');
            document.getElementById('customer-name').textContent = customer.name + ' (' + customer.citizen_id + ')';
            const badge = document.getElementById('membership-badge');
            badge.textContent = customer.active_membership ? '{{ __("สมาชิก") }}' : '{{ __("ไม่เป็นสมาชิก") }}';
            badge.className = customer.active_membership ? 'ms-2 badge bg-success' : 'ms-2 badge bg-secondary';
        }
        renderCart();
    });
</script>
@endpush
@endsection
