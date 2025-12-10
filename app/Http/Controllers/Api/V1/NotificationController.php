<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->notifications);
    }

    /**
     * Get unread notifications count and list.
     */
    public function unread(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications->count(),
            'notifications' => $request->user()->unreadNotifications
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'تم قراءة الإشعار']);
        }

        return response()->json(['message' => 'الإشعار غير موجود'], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم قراءة جميع الإشعارات']);
    }
}
