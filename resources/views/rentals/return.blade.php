<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Process Return — Rental #{{ $rental->id }}</h2></x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <p class="text-sm text-gray-600 mb-4">Customer: <strong>{{ $rental->customer->name }}</strong></p>
                <form method="POST" action="{{ route('rentals.process-return', $rental) }}">
                    @csrf
                    <h3 class="font-medium text-gray-700 mb-3">Select items to return:</h3>
                    @forelse($pendingItems as $item)
                    <label class="flex items-center gap-3 p-3 border rounded mb-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" checked class="rounded">
                        <div class="flex-1">
                            <span class="font-medium">{{ $item->product->name }}</span>
                            <span class="text-sm text-gray-500 ml-2">Qty: {{ $item->qty }}</span>
                            <span class="text-sm text-gray-500 ml-2">Deposit: ฿{{ number_format($item->deposit_amount, 2) }}</span>
                        </div>
                    </label>
                    @empty
                    <p class="text-gray-500">All items already returned.</p>
                    @endforelse
                    @if($pendingItems->isNotEmpty())
                    <div class="flex gap-3 mt-4">
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded">Process Return</button>
                        <a href="{{ route('rentals.show', $rental) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded">Cancel</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
