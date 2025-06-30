<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * عرض جميع إشعارات المستخدم
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = config('constants.pagination.per_page');
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * عرض الإشعارات غير المقروءة فقط
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        $perPage = config('constants.pagination.per_page');
        
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('notifications.unread', compact('notifications'));
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // التأكد من أن الإشعار يخص المستخدم الحالي
        if ($notification->user_id !== $request->user()->id) {
            abort(403, config('constants.error_messages.forbidden'));
        }

        NotificationService::markAsRead($notification);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', config('constants.success_messages.notification.marked_read'));
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        NotificationService::markAllAsRead($user);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', config('constants.success_messages.notification.marked_all_read'));
    }

    /**
     * حذف إشعار
     */
    public function destroy(Request $request, Notification $notification)
    {
        // التأكد من أن الإشعار يخص المستخدم الحالي
        if ($notification->user_id !== $request->user()->id) {
            abort(403, config('constants.error_messages.forbidden'));
        }

        NotificationService::deleteNotification($notification);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', config('constants.success_messages.notification.deleted'));
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll(Request $request)
    {
        $user = $request->user();
        NotificationService::deleteAllNotifications($user);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', config('constants.success_messages.notification.deleted_all'));
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة (لـ AJAX)
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على آخر الإشعارات (لـ AJAX)
     */
    public function latest(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }
}
