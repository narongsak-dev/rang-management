<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customer: {{ $customer->name }}</h2>
            <a href="{{ route('customers.edit', $customer) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Edit</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Customer Info</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500">Citizen ID</dt><dd class="font-mono">{{ $customer->citizen_id }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd>{{ $customer->phone ?? '-' }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Address</dt><dd>{{ $customer->address ?? '-' }}</dd></div>
                </dl>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">Memberships</h3>
                    <a href="{{ route('memberships.create') }}?customer_id={{ $customer->id }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">+ New Membership</a>
                </div>
                @forelse($customer->memberships as $m)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <div>
                        <span class="font-mono font-medium">{{ $m->member_no }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ $m->started_at->format('d/m/Y') }} – {{ $m->expires_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $m->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $m->status }}</span>
                        <form method="POST" action="{{ route('memberships.renew', $m) }}">@csrf
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-900">Renew</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No memberships yet.</p>
                @endforelse
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Recent Sales</h3>
                @forelse($customer->sales as $sale)
                <div class="flex justify-between text-sm py-2 border-b last:border-0">
                    <a href="{{ route('pos.receipt', $sale) }}" class="font-mono text-indigo-600">{{ $sale->order_no }}</a>
                    <span>฿{{ number_format($sale->total, 2) }}</span>
                    <span class="text-gray-400">{{ $sale->paid_at?->format('d/m/Y H:i') }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No sales yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
