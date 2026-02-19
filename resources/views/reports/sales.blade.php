<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Report</h2></x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-3 mb-4">
                    <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-3 py-2">
                    <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-3 py-2">
                    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                </form>
                <div class="mb-4 text-lg font-semibold">Total: ฿{{ number_format($total, 2) }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr>
                            <td class="px-4 py-3"><a href="{{ route('pos.receipt', $sale) }}" class="font-mono text-indigo-600 hover:text-indigo-900">{{ $sale->order_no }}</a></td>
                            <td class="px-4 py-3">{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                            <td class="px-4 py-3">{{ $sale->staff->name }}</td>
                            <td class="px-4 py-3 text-right">฿{{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($sale->payment_method) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $sale->paid_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No sales found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $sales->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
