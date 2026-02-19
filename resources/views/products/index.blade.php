<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
            @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">+ Add Product</a>
            @endcan
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-3 mb-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/barcode..." class="border rounded px-3 py-2 flex-1">
                    <select name="type" class="border rounded px-3 py-2">
                        <option value="">All Types</option>
                        @foreach(['sale','rent','service','fee'] as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Search</button>
                </form>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barcode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Available</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr>
                            <td class="px-4 py-3 font-mono text-sm">{{ $product->barcode }}</td>
                            <td class="px-4 py-3">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ match($product->type) { 'sale'=>'bg-blue-100 text-blue-800', 'rent'=>'bg-green-100 text-green-800', 'service'=>'bg-yellow-100 text-yellow-800', default=>'bg-gray-100 text-gray-800' } }}">{{ $product->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">฿{{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->stock_qty }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->available_qty }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('update', $product)
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
