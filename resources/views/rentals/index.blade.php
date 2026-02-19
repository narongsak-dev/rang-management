<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Rentals</h2></x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-3 mb-4">
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">All Status</option>
                        <option value="open" @selected(request('status')==='open')>Open</option>
                        <option value="returned" @selected(request('status')==='returned')>Returned</option>
                    </select>
                    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                </form>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rental ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rented At</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rentals as $rental)
                        <tr>
                            <td class="px-4 py-3 font-mono text-sm">#{{ $rental->id }}</td>
                            <td class="px-4 py-3">{{ $rental->customer->name }}</td>
                            <td class="px-4 py-3 font-mono text-sm">{{ $rental->sale->order_no }}</td>
                            <td class="px-4 py-3 text-sm">{{ $rental->rented_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $rental->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">{{ $rental->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('rentals.show', $rental) }}" class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                @if($rental->status === 'open')
                                <a href="{{ route('rentals.return', $rental) }}" class="text-orange-600 hover:text-orange-900 text-sm">Return</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No rentals found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $rentals->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
