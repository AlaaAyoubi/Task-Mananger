<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * إرسال إشعار عند تغيير حالة المهمة
     */
    public static function taskStatusChanged(Task $task, User $changedBy, string $oldStatus, string $newStatus)
    {
        $message = "تم تغيير حالة المهمة '{$task->title}' من '{$oldStatus}' إلى '{$newStatus}' بواسطة {$changedBy->name}";
        
        // إرسال إشعار للمدير والأدمن
        $managersAndAdmins = User::whereHas('teams', function ($query) use ($task) {
            $query->where('team_id', $task->team_id)
                  ->whereIn('role', ['manager', 'admin']);
        })->orWhereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($managersAndAdmins as $user) {
            if ($user->id !== $changedBy->id) { // لا ترسل إشعار لمن قام بالتغيير
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_status_changed',
                    'message' => $message,
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'changed_by' => $changedBy->name,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'team_id' => $task->team_id,
                    ],
                ]);
            }
        }
    }

    /**
     * إرسال إشعار عند إنشاء مهمة جديدة
     */
    public static function taskCreated(Task $task, User $createdBy)
    {
        $message = "تم إنشاء مهمة جديدة '{$task->title}' بواسطة {$createdBy->name}";
        
        // إرسال إشعار للعضو المكلف بالمهمة
        if ($task->user_id && $task->user_id !== $createdBy->id) {
            Notification::create([
                'user_id' => $task->user_id,
                'type' => 'task_created',
                'message' => $message,
                'data' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'created_by' => $createdBy->name,
                    'team_id' => $task->team_id,
                ],
            ]);
        }

        // إرسال إشعار للمدير والأدمن الآخرين
        $managersAndAdmins = User::whereHas('teams', function ($query) use ($task) {
            $query->where('team_id', $task->team_id)
                  ->whereIn('role', ['manager', 'admin']);
        })->orWhereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($managersAndAdmins as $user) {
            if ($user->id !== $createdBy->id) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_created',
                    'message' => $message,
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'created_by' => $createdBy->name,
                        'assigned_to' => $task->user->name ?? 'غير محدد',
                        'team_id' => $task->team_id,
                    ],
                ]);
            }
        }
    }

    /**
     * إرسال إشعار عند تحديث مهمة
     */
    public static function taskUpdated(Task $task, User $updatedBy, array $changes)
    {
        $message = "تم تحديث المهمة '{$task->title}' بواسطة {$updatedBy->name}";
        
        // إرسال إشعار للعضو المكلف بالمهمة
        if ($task->user_id && $task->user_id !== $updatedBy->id) {
            Notification::create([
                'user_id' => $task->user_id,
                'type' => 'task_updated',
                'message' => $message,
                'data' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'updated_by' => $updatedBy->name,
                    'changes' => $changes,
                    'team_id' => $task->team_id,
                ],
            ]);
        }

        // إرسال إشعار للمدير والأدمن الآخرين
        $managersAndAdmins = User::whereHas('teams', function ($query) use ($task) {
            $query->where('team_id', $task->team_id)
                  ->whereIn('role', ['manager', 'admin']);
        })->orWhereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($managersAndAdmins as $user) {
            if ($user->id !== $updatedBy->id) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_updated',
                    'message' => $message,
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'updated_by' => $updatedBy->name,
                        'changes' => $changes,
                        'assigned_to' => $task->user->name ?? 'غير محدد',
                        'team_id' => $task->team_id,
                    ],
                ]);
            }
        }
    }

    /**
     * تحديد إشعار كمقروء
     */
    public static function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
    }

    /**
     * تحديد جميع إشعارات المستخدم كمقروءة
     */
    public static function markAllAsRead(User $user)
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * حذف إشعار
     */
    public static function deleteNotification(Notification $notification)
    {
        $notification->delete();
    }

    /**
     * حذف جميع إشعارات المستخدم
     */
    public static function deleteAllNotifications(User $user)
    {
        Notification::where('user_id', $user->id)->delete();
    }
} 