<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products'  => Product::count(),
            'active_products' => Product::active()->count(),
            'today_sales'     => Sale::whereDate('paid_at', today())->count(),
            'today_revenue'   => Sale::whereDate('paid_at', today())->sum('total'),
            'open_rentals'    => Rental::where('status', 'open')->count(),
            'total_customers' => Customer::count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
