<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Receipt</h2>
            <div class="space-x-2">
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Print</button>
                <a href="{{ route('pos.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">New Sale</a>
            </div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8" id="receipt">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold">🎯 GunRange Management</h1>
                    <p class="text-gray-500 text-sm">Official Receipt</p>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Receipt No:</span>
                    <span class="font-mono">{{ $sale->receipt?->receipt_no ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Order No:</span>
                    <span class="font-mono">{{ $sale->order_no }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Date:</span>
                    <span>{{ $sale->paid_at?->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Staff:</span>
                    <span>{{ $sale->staff->name }}</span>
                </div>
                @if($sale->customer)
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Customer:</span>
                    <span>{{ $sale->customer->name }}</span>
                </div>
                @endif
                <div class="border-t border-b my-4 py-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-gray-500">
                                <th class="text-left py-1">Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="py-1">{{ $item->product->name }}{{ $item->is_rental ? ' [RENT]' : '' }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-right">฿{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">฿{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span>฿{{ number_format($sale->subtotal, 2) }}</span></div>
                    @if($sale->discount > 0)
                    <div class="flex justify-between text-red-600"><span>Discount</span><span>-฿{{ number_format($sale->discount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-lg border-t pt-1"><span>TOTAL</span><span>฿{{ number_format($sale->total, 2) }}</span></div>
                    <div class="flex justify-between text-gray-500"><span>Payment</span><span>{{ strtoupper($sale->payment_method) }}</span></div>
                </div>
                <div class="text-center mt-6 text-gray-400 text-xs">Thank you for visiting GunRange!</div>
            </div>
        </div>
    </div>
</x-app-layout>
