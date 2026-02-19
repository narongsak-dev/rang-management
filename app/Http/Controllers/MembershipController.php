<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Membership;
use App\Services\MembershipService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(private MembershipService $membershipService) {}

    public function index(Request $request)
    {
        $query = Membership::with('customer')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('citizen_id', 'like', '%' . $request->search . '%');
            })->orWhere('member_no', 'like', '%' . $request->search . '%');
        }
        $memberships = $query->paginate(20)->withQueryString();
        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('memberships.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate(['customer_id' => 'required|exists:customers,id']);
        $customer = Customer::findOrFail($request->customer_id);

        if ($customer->activeMembership) {
            return back()->withErrors(['customer_id' => 'Customer already has an active membership. Please renew instead.']);
        }

        $membership = $this->membershipService->create($customer);
        return redirect()->route('memberships.index')->with('success', "Membership {$membership->member_no} created.");
    }

    public function renew(Membership $membership)
    {
        $this->membershipService->renew($membership);
        return back()->with('success', 'Membership renewed for 1 year.');
    }
}
