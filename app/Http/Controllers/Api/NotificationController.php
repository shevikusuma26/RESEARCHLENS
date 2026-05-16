<?php

namespace App\Http\Controllers\Api;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        try {
            $notifications = UserNotification::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('limit', 15));

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved',
                'data'    => $notifications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getUnreadCount(Request $request)
    {
        $count = UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data'    => ['unread_count' => $count],
        ], 200);
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = UserNotification::where('user_id', $request->user()->id)
                ->findOrFail($id);
            $notification->update(['is_read' => true]);

            return response()->json(['success' => true, 'message' => 'Marked as read'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function markAllAsRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read'], 200);
    }

    public function delete(Request $request, $id)
    {
        try {
            $notification = UserNotification::where('user_id', $request->user()->id)
                ->findOrFail($id);
            $notification->delete();

            return response()->json(['success' => true, 'message' => 'Notification deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
