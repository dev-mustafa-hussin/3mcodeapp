<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 1. Sales Report
    public function sales(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Total Sales Amount
        $totalSales = Sale::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('grand_total');

        // Sales count
        $salesCount = Sale::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        // Chart Data (Daily Sales)
        $dailySales = Sale::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Selling Products
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.date', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'sales_count' => $salesCount,
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
        ]);
    }

    // 2. Purchases Report
    public function purchases(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $totalPurchases = Purchase::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'received')
            ->sum('grand_total');

        $purchasesCount = Purchase::whereBetween('date', [$startDate, $endDate])
             ->where('status', 'received')
             ->count();

        $dailyPurchases = Purchase::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'received')
            ->selectRaw('date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_purchases' => $totalPurchases,
            'purchases_count' => $purchasesCount,
            'daily_purchases' => $dailyPurchases,
        ]);
    }

    // 3. Inventory Report
    public function stock()
    {
        $totalProducts = Product::count();
        $totalStockValue = Product::sum(DB::raw('price * current_stock')); // Approximate value based on selling price, or use cost if available
        
        $lowStockProducts = Product::where('current_stock', '<=', 10)->get();

        $categoryDistribution = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return response()->json([
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'low_stock_products' => $lowStockProducts,
            'category_distribution' => $categoryDistribution,
        ]);
    }
    
    // 4. Profit & Loss (Simplified)
    public function profitLoss(Request $request)
    {
         $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
         $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());
         
         $revenue = Sale::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('grand_total');
            
         $expenses = Purchase::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'received')
            ->sum('grand_total');
            
         return response()->json([
             'revenue' => $revenue,
             'expenses' => $expenses,
             'net_profit' => $revenue - $expenses // Simplified logic
         ]);
    }
}
