<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_warehouses' => Warehouse::count(),
            'total_sales' => Sale::count(),
            'low_stock_items' => Inventory::whereColumn('quantity', '<=', 'minimum_stock')->count(),
        ];

        $recentSales = Sale::with('saleItems')
            ->latest()
            ->take(5)
            ->get();

        $lowStockItems = Inventory::with(['product', 'warehouse'])
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->orderByRaw('(quantity/minimum_stock) ASC')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentSales', 'lowStockItems'));
    }
}
