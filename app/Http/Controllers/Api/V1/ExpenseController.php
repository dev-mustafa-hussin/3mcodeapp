<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['category', 'warehouse'])->latest()->get();
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['ref_no'] = 'EXP-' . time(); // Simple ref generator

        $expense = Expense::create($validated);
        return response()->json($expense, 201);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return response()->json(['message' => 'deleted']);
    }

    // Categories
    public function categories()
    {
        return response()->json(ExpenseCategory::all());
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = ExpenseCategory::create($validated);
        return response()->json($category, 201);
    }
}
