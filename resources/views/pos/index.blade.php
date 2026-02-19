@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Point of Sale</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Cart Panel -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Cart Items</h5>
            </div>
            <div class="card-body">
                <!-- Customer Search -->
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-medium">Customer (by Citizen ID)</label>
                    <div class="input-group">
                        <input type="text" id="citizen-id-input" placeholder="Enter citizen ID..." class="form-control">
                        <button onclick="searchCustomer()" class="btn btn-primary">Search</button>
                        <button onclick="clearCustomer()" class="btn btn-secondary">Clear</button>
                    </div>
                    <div id="customer-info" class="mt-2 d-none">
                        <span class="fw-medium text-success" id="customer-name"></span>
                        <span class="ms-2 badge" id="membership-badge"></span>
                    </div>
                </div>
                <!-- Product Scan -->
                <div class="mb-4">
                    <label class="form-label fw-medium">Scan Barcode</label>
                    <div class="input-group">
                        <input type="text" id="barcode-input" placeholder="Scan or type barcode..." class="form-control" onkeydown="if(event.key==='Enter') addItem()">
                        <button onclick="addItem()" class="btn btn-primary">Add</button>
                    </div>
                    <div id="scan-error" class="text-danger small mt-1 d-none"></div>
                </div>
                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <tr id="empty-row"><td colspan="6" class="text-center text-muted py-4">No items in cart</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Panel -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Checkout</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span id="subtotal">฿0.00</span>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span>Discount</span>
                    <input type="number" id="discount" value="0" min="0" step="0.01" class="form-control w-auto text-end" style="max-width:120px;" oninput="updateTotals()">
                </div>
                <div class="mb-3 d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                    <span>Total</span>
                    <span id="total">฿0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select id="payment-method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <button onclick="checkout()" id="checkout-btn" class="btn btn-success w-100 py-3 fw-bold fs-5" disabled>Checkout</button>
                <div id="checkout-error" class="text-danger small mt-2 d-none"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];
let customer = null;

async function searchCustomer() {
    const citizenId = document.getElementById('citizen-id-input').value.trim();
    if (!citizenId) return;
    const res = await fetch(`{{ route('customers.search') }}?citizen_id=${citizenId}`, {
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
    });
    const data = await res.json();
    if (data.found) {
        customer = data.customer;
        document.getElementById('customer-info').classList.remove('d-none');
        document.getElementById('customer-name').textContent = customer.name + ' (' + customer.citizen_id + ')';
        const badge = document.getElementById('membership-badge');
        if (customer.active_membership) {
            badge.textContent = 'MEMBER';
            badge.className = 'ms-2 badge bg-success';
        } else {
            badge.textContent = 'No Membership';
            badge.className = 'ms-2 badge bg-secondary';
        }
    } else {
        alert('Customer not found. Please add customer first.');
        customer = null;
    }
}

function clearCustomer() {
    customer = null;
    document.getElementById('citizen-id-input').value = '';
    document.getElementById('customer-info').classList.add('d-none');
}

async function addItem() {
    const barcode = document.getElementById('barcode-input').value.trim();
    if (!barcode) return;
    document.getElementById('scan-error').classList.add('d-none');
    try {
        const res = await fetch('{{ route("pos.scan") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
            body: JSON.stringify({barcode})
        });
        const data = await res.json();
        if (!data.found) { showScanError(data.message || 'Product not found'); return; }
        const product = data.product;
        const existing = cart.find(i => i.product_id === product.id && i.is_rental === (product.type === 'rent'));
        if (existing) { existing.qty++; existing.line_total = existing.unit_price * existing.qty; }
        else { cart.push({ product_id: product.id, name: product.name, type: product.type, qty: 1, unit_price: parseFloat(product.price), line_total: parseFloat(product.price), is_rental: product.type === 'rent', available_qty: product.available_qty }); }
        document.getElementById('barcode-input').value = '';
        renderCart();
    } catch(e) { showScanError('Error scanning product'); }
}

function showScanError(msg) {
    const el = document.getElementById('scan-error');
    el.textContent = msg;
    el.classList.remove('d-none');
}

function removeItem(idx) { cart.splice(idx, 1); renderCart(); }
function updateQty(idx, qty) { cart[idx].qty = parseInt(qty); cart[idx].line_total = cart[idx].unit_price * cart[idx].qty; renderCart(); }

function renderCart() {
    const tbody = document.getElementById('cart-body');
    if (cart.length === 0) { tbody.innerHTML = '<tr id="empty-row"><td colspan="6" class="text-center text-muted py-4">No items in cart</td></tr>'; document.getElementById('checkout-btn').disabled = true; updateTotals(); return; }
    tbody.innerHTML = cart.map((item, idx) => `
        <tr>
            <td>${item.name}</td>
            <td class="text-center"><span class="badge ${item.is_rental ? 'bg-success' : 'bg-primary'}">${item.type}</span></td>
            <td class="text-center"><input type="number" min="1" value="${item.qty}" onchange="updateQty(${idx}, this.value)" class="form-control form-control-sm text-center" style="width:70px;margin:auto;"></td>
            <td class="text-end">฿${item.unit_price.toFixed(2)}</td>
            <td class="text-end fw-medium">฿${item.line_total.toFixed(2)}</td>
            <td class="text-center"><button onclick="removeItem(${idx})" class="btn btn-sm btn-outline-danger">✕</button></td>
        </tr>`).join('');
    document.getElementById('checkout-btn').disabled = false;
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((s, i) => s + i.line_total, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = Math.max(0, subtotal - discount);
    document.getElementById('subtotal').textContent = '฿' + subtotal.toFixed(2);
    document.getElementById('total').textContent = '฿' + total.toFixed(2);
}

async function checkout() {
    document.getElementById('checkout-error').classList.add('d-none');
    const hasRental = cart.some(i => i.is_rental);
    if (hasRental && !customer) { document.getElementById('checkout-error').textContent = 'Customer required for rental items.'; document.getElementById('checkout-error').classList.remove('d-none'); return; }
    const payload = {
        items: cart.map(i => ({ product_id: i.product_id, qty: i.qty, unit_price: i.unit_price, is_rental: i.is_rental })),
        payment_method: document.getElementById('payment-method').value,
        discount: parseFloat(document.getElementById('discount').value) || 0,
        customer_id: customer ? customer.id : null,
    };
    try {
        const res = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) { window.location.href = data.receipt_url; }
        else { document.getElementById('checkout-error').textContent = data.error || 'Checkout failed.'; document.getElementById('checkout-error').classList.remove('d-none'); }
    } catch(e) { document.getElementById('checkout-error').textContent = 'Network error.'; document.getElementById('checkout-error').classList.remove('d-none'); }
}
</script>
@endpush
@endsection
