<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rental #{{ $rental->id }}</h2>
            @if($rental->status === 'open')
            <a href="{{ route('rentals.return', $rental) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm">Process Return</a>
            @endif
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-3 gap-3 text-sm">
                    <div><dt class="text-gray-500">Customer</dt><dd>{{ $rental->customer->name }}</dd></div>
                    <div><dt class="text-gray-500">Order</dt><dd class="font-mono">{{ $rental->sale->order_no }}</dd></div>
                    <div><dt class="text-gray-500">Rented At</dt><dd>{{ $rental->rented_at->format('d/m/Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd><span class="px-2 py-1 text-xs rounded-full {{ $rental->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">{{ $rental->status }}</span></dd></div>
                </dl>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Rental Items</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deposit</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Returned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($rental->items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->product->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right">฿{{ number_format($item->deposit_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->returned_at)
                                <span class="text-green-600 text-sm">{{ $item->returned_at->format('d/m/Y H:i') }}</span>
                                @else
                                <span class="text-yellow-600 text-sm">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
