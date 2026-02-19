<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2></x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-6">
                <a href="{{ route('reports.sales') }}" class="block bg-white shadow-sm sm:rounded-lg p-8 text-center hover:shadow-md">
                    <div class="text-4xl mb-3">💰</div>
                    <div class="text-lg font-bold text-gray-800">Sales Report</div>
                    <div class="text-sm text-gray-500 mt-1">View sales history and revenue</div>
                </a>
                @can('update', App\Models\Product::class)
                <a href="{{ route('reports.inventory') }}" class="block bg-white shadow-sm sm:rounded-lg p-8 text-center hover:shadow-md">
                    <div class="text-4xl mb-3">📦</div>
                    <div class="text-lg font-bold text-gray-800">Inventory Report</div>
                    <div class="text-sm text-gray-500 mt-1">View stock levels and movements</div>
                </a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
