<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $totalSales = Invoice::where('status', 'paid')->sum('total');
        $invoicesCount = Invoice::count();
        $productsCount = Product::count();
        $customersCount = Customer::count();

        // Top 5 Products
        $topProducts = DB::table('invoice_items')
            ->select('products.name', DB::raw('SUM(invoice_items.quantity) as total_quantity'))
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Invoice Status Distribution
        $invoiceStatus = Invoice::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'invoices_count' => $invoicesCount,
            'products_count' => $productsCount,
            'customers_count' => $customersCount,
            'top_products' => $topProducts,
            'invoice_status_distribution' => $invoiceStatus
        ]);
    }
}
