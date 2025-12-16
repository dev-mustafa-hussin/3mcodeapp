<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])->latest()->paginate(20);
        return response()->json($transfers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'date' => 'required|date',
            'status' => 'required|in:pending,sent,completed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('items');
            $data['ref_no'] = 'ST-' . time();
            // $data['created_by'] = auth()->id(); // Uncomment when auth is fully rigorous

            $transfer = StockTransfer::create($data);

            foreach ($request->items as $item) {
                // We create the item record
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    // 'unit_cost' => ... (fetch from product if needed)
                    // 'subtotal' => ...
                ]);
                
                // NO Stock Deduction logic here as per plan (Global Stock System)
            }

            DB::commit();
            return response()->json($transfer->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create transfer', 'error' => $e->getMessage()], 500);
        }
    }
}
