<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['supplier', 'items.product', 'purchase'])->latest();

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $returns = $query->paginate(20);
        return response()->json($returns);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'status' => 'required|in:completed,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('items');
            $data['reference_number'] = 'PR-' . time() . '-' . rand(1000, 9999);
            
            $purchaseReturn = PurchaseReturn::create($data);

            foreach ($request->items as $item) {
                $purchaseReturn->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                    'tax' => $item['tax'] ?? 0,
                    'total' => ($item['quantity'] * $item['unit_cost']) + ($item['tax'] ?? 0),
                ]);
                
                // Optional: Decrement Product Stock since we are returning items (outgoing from our stock back to supplier)
                // Wait, if we return to supplier, stock decreases.
                // Logic: Purchase (Stock In) -> Return (Stock Out).
                // if ($request->status === 'completed') {
                //      $product = Product::find($item['product_id']);
                //      $product->decrement('current_stock', $item['quantity']);
                // }
            }

            DB::commit();
            return response()->json($purchaseReturn->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create return', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $purchaseReturn = PurchaseReturn::with(['supplier', 'items.product'])->find($id);
        if (!$purchaseReturn) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($purchaseReturn);
    }

    public function destroy($id)
    {
        $purchaseReturn = PurchaseReturn::find($id);
        if (!$purchaseReturn) return response()->json(['message' => 'Not Found'], 404);
        $purchaseReturn->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
