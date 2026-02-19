<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Products</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Today Sales</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['today_sales'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Today Revenue</div>
                    <div class="text-3xl font-bold text-green-600">฿{{ number_format($stats['today_revenue'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Open Rentals</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['open_rentals'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Customers</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_customers'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Active Products</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['active_products'] }}</div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                @hasrole('cashier|admin')
                <a href="{{ route('pos.index') }}" class="block bg-indigo-600 hover:bg-indigo-700 text-white text-center py-6 rounded-lg shadow font-bold text-xl">
                    🛒 Go to POS
                </a>
                @endhasrole
                @hasrole('inventory|admin')
                <a href="{{ route('products.index') }}" class="block bg-gray-700 hover:bg-gray-800 text-white text-center py-6 rounded-lg shadow font-bold text-xl">
                    📦 Inventory
                </a>
                @endhasrole
            </div>
        </div>
    </div>
</x-app-layout>
