<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'low_stock'       => Product::where('type', 'sale')->where('stock_qty', '<=', 5)->count(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function stats()
    {
        // Sales last 7 days
        $sales7days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $sales7days[] = [
                'date'    => $date,
                'label'   => now()->subDays($i)->locale('th')->isoFormat('D MMM'),
                'revenue' => (float) Sale::whereDate('paid_at', $date)->sum('total'),
                'count'   => Sale::whereDate('paid_at', $date)->count(),
            ];
        }

        // Monthly by type (last 6 months)
        $monthlyByType = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->locale('th')->isoFormat('MMM YY');
            $y = $month->year;
            $m = $month->month;

            $saleRevenue = Sale::whereYear('paid_at', $y)
                ->whereMonth('paid_at', $m)
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->where('sale_items.is_rental', false)
                ->sum('sale_items.line_total');

            $rentRevenue = Sale::whereYear('paid_at', $y)
                ->whereMonth('paid_at', $m)
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->where('sale_items.is_rental', true)
                ->sum('sale_items.line_total');

            $monthlyByType[] = [
                'label'   => $label,
                'sale'    => (float) $saleRevenue,
                'rent'    => (float) $rentRevenue,
            ];
        }

        return response()->json([
            'sales_7days'    => $sales7days,
            'monthly_by_type' => $monthlyByType,
            'cards' => [
                'today_revenue' => (float) Sale::whereDate('paid_at', today())->sum('total'),
                'open_rentals'  => Rental::where('status', 'open')->count(),
                'low_stock'     => Product::where('type', 'sale')->where('stock_qty', '<=', 5)->count(),
            ],
        ]);
    }
}
