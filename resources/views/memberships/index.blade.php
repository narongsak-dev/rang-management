<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Memberships</h2>
            <a href="{{ route('memberships.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">+ New Membership</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-3 mb-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search member no / customer..." class="border rounded px-3 py-2 flex-1">
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">All</option>
                        <option value="active" @selected(request('status')==='active')>Active</option>
                        <option value="expired" @selected(request('status')==='expired')>Expired</option>
                    </select>
                    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Search</button>
                </form>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($memberships as $m)
                        <tr>
                            <td class="px-4 py-3 font-mono text-sm">{{ $m->member_no }}</td>
                            <td class="px-4 py-3"><a href="{{ route('customers.show', $m->customer) }}" class="text-indigo-600 hover:text-indigo-900">{{ $m->customer->name }}</a></td>
                            <td class="px-4 py-3 text-sm">{{ $m->started_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $m->expires_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $m->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $m->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('memberships.renew', $m) }}" class="inline">@csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">Renew</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No memberships found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $memberships->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
