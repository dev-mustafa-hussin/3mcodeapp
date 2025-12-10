<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Get all permission requests for the authenticated user.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->permissionRequests);
    }

    /**
     * Submit a new permission request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'permission' => 'required|string|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        $permissionRequest = $request->user()->permissionRequests()->create([
            'permission' => $request->permission,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'تم إرسال طلب الصلاحية بنجاح',
            'request' => $permissionRequest
        ], 201);
    }
}
