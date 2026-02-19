<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalItem;
use App\Services\RentalService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function __construct(private RentalService $rentalService) {}

    public function index(Request $request)
    {
        $query = Rental::with(['customer', 'sale'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $rentals = $query->paginate(20)->withQueryString();
        return view('rentals.index', compact('rentals'));
    }

    public function show(Rental $rental)
    {
        $rental->load(['items.product', 'customer', 'sale']);
        return view('rentals.show', compact('rental'));
    }

    public function returnForm(Rental $rental)
    {
        $rental->load(['items.product', 'customer']);
        $pendingItems = $rental->items()->whereNull('returned_at')->with('product')->get();
        return view('rentals.return', compact('rental', 'pendingItems'));
    }

    public function processReturn(Request $request, Rental $rental)
    {
        $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:rental_items,id',
        ]);

        foreach ($request->item_ids as $itemId) {
            $item = RentalItem::where('id', $itemId)
                ->where('rental_id', $rental->id)
                ->whereNull('returned_at')
                ->firstOrFail();
            $this->rentalService->returnItem($item);
        }

        $rental->refresh();
        $msg = $rental->status === 'returned'
            ? 'All items returned. Rental closed.'
            : 'Selected items returned.';

        return redirect()->route('rentals.show', $rental)->with('success', $msg);
    }
}
