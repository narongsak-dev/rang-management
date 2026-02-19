<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product: {{ $product->name }}</h2></x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf @method('PUT')
                    @include('products._form')
                    <div class="flex gap-3 mt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">Update</button>
                        <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded">Cancel</a>
                    </div>
                </form>

                @can('adjust', $product)
                <div class="mt-8 border-t pt-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Stock Management</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('products.stock-in', $product) }}">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock In</label>
                            <div class="flex gap-2">
                                <input type="number" name="qty" min="1" placeholder="Qty" class="border rounded px-3 py-2 w-24">
                                <input type="text" name="note" placeholder="Note" class="border rounded px-3 py-2 flex-1">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">+In</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('products.adjust', $product) }}">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adjust Stock (current: {{ $product->stock_qty }})</label>
                            <div class="flex gap-2">
                                <input type="number" name="new_qty" min="0" value="{{ $product->stock_qty }}" class="border rounded px-3 py-2 w-24">
                                <input type="text" name="note" placeholder="Reason" class="border rounded px-3 py-2 flex-1">
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded">Adjust</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan

                <div class="mt-6 border-t pt-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Recent Movements</h3>
                    @foreach($product->inventoryMovements()->with('creator')->latest()->limit(5)->get() as $m)
                    <div class="flex justify-between text-sm py-1">
                        <span class="font-medium {{ $m->type==='in'?'text-green-600':($m->type==='out'?'text-red-600':'text-yellow-600') }}">{{ strtoupper($m->type) }} {{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</span>
                        <span class="text-gray-500">{{ $m->note }}</span>
                        <span class="text-gray-400">{{ $m->creator->name }} · {{ $m->created_at->diffForHumans() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
