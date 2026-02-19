<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $query = Sale::with(['customer', 'staff'])->orderBy('paid_at', 'desc');
        if ($request->filled('from')) {
            $query->whereDate('paid_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('paid_at', '<=', $request->to);
        }
        $sales = $query->paginate(30)->withQueryString();
        $total = $query->sum('total');
        return view('reports.sales', compact('sales', 'total'));
    }

    public function inventory()
    {
        $products = Product::orderBy('name')->paginate(30);
        return view('reports.inventory', compact('products'));
    }
}
