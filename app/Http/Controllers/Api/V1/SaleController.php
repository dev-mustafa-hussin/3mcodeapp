<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'items.product'])->latest();

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $sales = $query->paginate(20);
        return response()->json($sales);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:completed,draft,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('items');
            $data['invoice_number'] = 'INV-' . time() . '-' . rand(1000, 9999);
            
            // Handle Walk-in Customer (null customer_id is allowed in migration)
            if (empty($data['customer_id'])) {
                $data['customer_id'] = null;
            }

            $sale = Sale::create($data);

            foreach ($request->items as $item) {
                // Get Product to check stock and price if needed, but we trust frontend price/cost for now or validation?
                // Better to fetch price from DB to prevent tampering, but for POS freedom we often allow overrides.
                // Let's stick to simple implementation first.
                
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'tax' => $item['tax'] ?? 0,
                    'total' => ($item['quantity'] * $item['unit_price']) + ($item['tax'] ?? 0),
                ]);

                // Stock Deduction Logic
                if ($request->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('current_stock', $item['quantity']);
                    }
                }
            }

            DB::commit();
            return response()->json($sale->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create sale', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'items.product'])->find($id);
        if (!$sale) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($sale);
    }
}
