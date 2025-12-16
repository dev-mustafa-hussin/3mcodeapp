<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DamagedStock;
use App\Models\Product; // Need to adjust stock later
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamagedStockController extends Controller
{
    public function index()
    {
        $damagedStocks = DamagedStock::with(['product', 'warehouse'])->latest()->get();
        return response()->json($damagedStocks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        $validated['ref_no'] = 'DMG-' . time();

        // In a real app, we should decrement stock from warehouse here inside a transaction
        // DB::transaction(function () use ($validated) { ... });

        $damagedStock = DamagedStock::create($validated);

        return response()->json($damagedStock, 201);
    }

    public function destroy($id)
    {
        $damagedStock = DamagedStock::findOrFail($id);
        // Should revert stock adjustment here
        $damagedStock->delete();
        return response()->json(['message' => 'deleted']);
    }
}
