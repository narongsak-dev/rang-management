<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">New Membership</h2></x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('memberships.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Customer *</label>
                        <select name="customer_id" class="mt-1 block w-full border rounded px-3 py-2" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id', request('customer_id')) == $c->id)>{{ $c->name }} ({{ $c->citizen_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">New membership will be valid for 1 year from today.</p>
                    <div class="flex gap-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">Create Membership</button>
                        <a href="{{ route('memberships.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
