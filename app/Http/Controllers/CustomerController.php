<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function index(Request $request)
    {
        $query = Customer::withCount('memberships');
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('citizen_id', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        $customers = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'citizen_id' => 'required|string|unique:customers',
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
        ]);
        $customer = Customer::create($data);
        $this->auditLogService->log('create', Customer::class, $customer->id, $data);
        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['memberships' => fn($q) => $q->latest(), 'sales' => fn($q) => $q->latest()->limit(10)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'citizen_id' => 'required|string|unique:customers,citizen_id,' . $customer->id,
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
        ]);
        $customer->update($data);
        $this->auditLogService->log('update', Customer::class, $customer->id, $data);
        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function search(Request $request)
    {
        $request->validate(['citizen_id' => 'required|string']);
        $customer = Customer::with('activeMembership')
            ->where('citizen_id', $request->citizen_id)
            ->first();

        return response()->json([
            'found'    => (bool) $customer,
            'customer' => $customer,
        ]);
    }
}
