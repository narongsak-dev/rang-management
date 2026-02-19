<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Point of Sale</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-6">
                <!-- Cart Panel -->
                <div class="col-span-2 bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Cart Items</h3>
                    <!-- Customer Search -->
                    <div class="mb-4 p-3 bg-gray-50 rounded border">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer (by Citizen ID)</label>
                        <div class="flex gap-2">
                            <input type="text" id="citizen-id-input" placeholder="Enter citizen ID..." class="border rounded px-3 py-2 flex-1">
                            <button onclick="searchCustomer()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Search</button>
                            <button onclick="clearCustomer()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 rounded">Clear</button>
                        </div>
                        <div id="customer-info" class="mt-2 hidden">
                            <span class="text-sm font-medium text-green-700" id="customer-name"></span>
                            <span class="ml-2 text-xs px-2 py-1 rounded-full" id="membership-badge"></span>
                        </div>
                    </div>
                    <!-- Product Scan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scan Barcode</label>
                        <div class="flex gap-2">
                            <input type="text" id="barcode-input" placeholder="Scan or type barcode..." class="border rounded px-3 py-2 flex-1" onkeydown="if(event.key==='Enter') addItem()">
                            <button onclick="addItem()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Add</button>
                        </div>
                        <div id="scan-error" class="text-red-600 text-sm mt-1 hidden"></div>
                    </div>
                    <!-- Cart Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Type</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Qty</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Price</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body" class="divide-y divide-gray-200">
                            <tr id="empty-row"><td colspan="6" class="px-3 py-6 text-center text-gray-400">No items in cart</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Checkout Panel -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Checkout</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm"><span>Subtotal</span><span id="subtotal">฿0.00</span></div>
                        <div class="flex justify-between items-center text-sm">
                            <span>Discount</span>
                            <input type="number" id="discount" value="0" min="0" step="0.01" class="border rounded px-2 py-1 w-28 text-right" oninput="updateTotals()">
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total</span><span id="total">฿0.00</span></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="payment-method" class="border rounded px-3 py-2 w-full">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <button onclick="checkout()" id="checkout-btn" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded font-bold text-lg mt-4" disabled>Checkout</button>
                        <div id="checkout-error" class="text-red-600 text-sm hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        document.getElementById('customer-info').classList.remove('hidden');
        document.getElementById('customer-name').textContent = customer.name + ' (' + customer.citizen_id + ')';
        const badge = document.getElementById('membership-badge');
        if (customer.active_membership) {
            badge.textContent = 'MEMBER';
            badge.className = 'ml-2 text-xs px-2 py-1 rounded-full bg-green-100 text-green-800';
        } else {
            badge.textContent = 'No Membership';
            badge.className = 'ml-2 text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500';
        }
    } else {
        alert('Customer not found. Please add customer first.');
        customer = null;
    }
}

function clearCustomer() {
    customer = null;
    document.getElementById('citizen-id-input').value = '';
    document.getElementById('customer-info').classList.add('hidden');
}

async function addItem() {
    const barcode = document.getElementById('barcode-input').value.trim();
    if (!barcode) return;
    document.getElementById('scan-error').classList.add('hidden');
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
    el.classList.remove('hidden');
}

function removeItem(idx) { cart.splice(idx, 1); renderCart(); }
function updateQty(idx, qty) { cart[idx].qty = parseInt(qty); cart[idx].line_total = cart[idx].unit_price * cart[idx].qty; renderCart(); }

function renderCart() {
    const tbody = document.getElementById('cart-body');
    if (cart.length === 0) { tbody.innerHTML = '<tr id="empty-row"><td colspan="6" class="px-3 py-6 text-center text-gray-400">No items in cart</td></tr>'; document.getElementById('checkout-btn').disabled = true; updateTotals(); return; }
    tbody.innerHTML = cart.map((item, idx) => `
        <tr>
            <td class="px-3 py-2 text-sm">${item.name}</td>
            <td class="px-3 py-2 text-center"><span class="px-2 py-0.5 text-xs rounded-full ${item.is_rental ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">${item.type}</span></td>
            <td class="px-3 py-2 text-center"><input type="number" min="1" value="${item.qty}" onchange="updateQty(${idx}, this.value)" class="border rounded px-2 py-1 w-16 text-center"></td>
            <td class="px-3 py-2 text-right text-sm">฿${item.unit_price.toFixed(2)}</td>
            <td class="px-3 py-2 text-right text-sm font-medium">฿${item.line_total.toFixed(2)}</td>
            <td class="px-3 py-2 text-center"><button onclick="removeItem(${idx})" class="text-red-500 hover:text-red-700">✕</button></td>
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
    document.getElementById('checkout-error').classList.add('hidden');
    const hasRental = cart.some(i => i.is_rental);
    if (hasRental && !customer) { document.getElementById('checkout-error').textContent = 'Customer required for rental items.'; document.getElementById('checkout-error').classList.remove('hidden'); return; }
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
        else { document.getElementById('checkout-error').textContent = data.error || 'Checkout failed.'; document.getElementById('checkout-error').classList.remove('hidden'); }
    } catch(e) { document.getElementById('checkout-error').textContent = 'Network error.'; document.getElementById('checkout-error').classList.remove('hidden'); }
}
</script>
</x-app-layout>
