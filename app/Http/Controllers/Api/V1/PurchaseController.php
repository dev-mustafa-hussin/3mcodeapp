<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'items.product'])->latest();

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Add date range filtering if needed later

        $purchases = $query->paginate(20);

        return response()->json($purchases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'status' => 'required|in:received,pending,ordered,canceled',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('items');
            $data['reference_number'] = 'PO-' . time() . '-' . rand(1000, 9999);
            
            // Calculate totals (can also enable frontend calculated values, but backend check is safer)
            // For now, trust frontend calculated totals or re-calculate
            
            $purchase = Purchase::create($data);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                    'tax' => $item['tax'] ?? 0,
                    'total' => ($item['quantity'] * $item['unit_cost']) + ($item['tax'] ?? 0),
                ]);
                
                // Optional: Update Product Stock here if status is 'received'
                // if ($request->status === 'received') {
                //      $product = Product::find($item['product_id']);
                //      $product->increment('current_stock', $item['quantity']);
                // }
            }

            DB::commit();

            return response()->json($purchase->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create purchase', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        return response()->json($purchase);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
             return response()->json(['message' => 'Purchase not found'], 404);
        }

        // Optional: Revert stock if needed
        $purchase->delete();

        return response()->json(['message' => 'Purchase deleted successfully']);
    }
}
