<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return response()->json(Warehouse::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $warehouse = Warehouse::create($data);
        return response()->json($warehouse, 201);
    }

    public function show($id)
    {
        return response()->json(Warehouse::findOrFail($id));
    }
    
    // update/destroy unimplemented for now as not required by current task
}
