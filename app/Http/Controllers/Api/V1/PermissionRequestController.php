<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermissionRequest;
use App\Mail\PermissionRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PermissionRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
            'requested_permission' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        $user = $request->user();

        // Save to DB
        $permissionRequest = PermissionRequest::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'permission_name' => $request->requested_permission,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Send Email to Admin (for now sending to the defined admin email or info@)
        try {
            // You might want to get all admins here. For now sending to the config mail or specific admin.
            Mail::to('info@alarabia-cosmetics.com')->send(new PermissionRequestMail($user, $request->requested_permission, $request->url));
        } catch (\Exception $e) {
            Log::error('Failed to send permission request email: ' . $e->getMessage());
            // Don't fail the request if email fails, but maybe warn
        }

        return response()->json(['message' => 'تم إرسال الطلب بنجاح'], 201);
    }
}
