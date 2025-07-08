<?php

return [
    // الأدوار
    'roles' => [
        'admin' => 'أدمن',
        'manager' => 'مدير',
        'member' => 'عضو',
    ],

    // حالات المهام
    'task_statuses' => [
        'pending' => 'قيد الانتظار',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتملة',
        'canceled' => 'ملغاة',
    ],

    // أولويات المهام
    'task_priorities' => [
        'high' => 'عالية',
        'medium' => 'متوسطة',
        'low' => 'منخفضة',
    ],

    // رسائل التحقق
    'validation_messages' => [
        'task' => [
            'title_required' => 'عنوان المهمة مطلوب',
            'title_max' => 'عنوان المهمة يجب أن لا يتجاوز 255 حرف',
            'status_required' => 'حالة المهمة مطلوبة',
            'status_invalid' => 'حالة المهمة غير صحيحة',
            'priority_required' => 'أولوية المهمة مطلوبة',
            'priority_invalid' => 'أولوية المهمة غير صحيحة',
            'due_date_date' => 'تاريخ الاستحقاق يجب أن يكون تاريخ صحيح',
            'assigned_user_required' => 'يجب تحديد عضو للمهمة',
            'assigned_user_exists' => 'العضو المحدد غير موجود',
            'team_required' => 'يجب تحديد فريق للمهمة',
            'team_exists' => 'الفريق المحدد غير موجود',
            'user_not_in_team' => 'العضو المحدد لا ينتمي إلى الفريق المحدد',
        ],
        'team' => [
            'name_required' => 'اسم الفريق مطلوب',
            'name_max' => 'اسم الفريق يجب أن لا يتجاوز 255 حرف',
            'description_max' => 'وصف الفريق يجب أن لا يتجاوز 1000 حرف',
        ],
    ],

    // رسائل النجاح
    'success_messages' => [
        'task' => [
            'created' => 'تم إنشاء المهمة بنجاح',
            'updated' => 'تم تحديث المهمة بنجاح',
            'deleted' => 'تم حذف المهمة بنجاح',
            'status_updated' => 'تم تحديث حالة المهمة بنجاح',
        ],
        'team' => [
            'created' => 'تم إنشاء الفريق بنجاح',
            'updated' => 'تم تحديث الفريق بنجاح',
            'deleted' => 'تم حذف الفريق بنجاح',
        ],
        'notification' => [
            'marked_read' => 'تم تحديد الإشعار كمقروء',
            'marked_all_read' => 'تم تحديد جميع الإشعارات كمقروءة',
            'deleted' => 'تم حذف الإشعار بنجاح',
            'deleted_all' => 'تم حذف جميع الإشعارات بنجاح',
        ],
    ],

    // رسائل الخطأ
    'error_messages' => [
        'unauthorized' => 'غير مصرح لك بالوصول إلى هذه الصفحة',
        'forbidden' => 'غير مصرح لك بتنفيذ هذا الإجراء',
        'not_found' => 'العنصر المطلوب غير موجود',
        'validation_failed' => 'فشل في التحقق من صحة البيانات',
    ],
]; 