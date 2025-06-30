# إصلاح مشكلة مهام المدير

## ❌ المشكلة الأصلية:
```
ErrorException: Undefined variable $managedTeamIds
```

## 🔍 أسباب المشكلة:

### 1. **متغير مفقود في المتحكم**
- **المشكلة**: دالة `index` في `TaskController` لا ترسل `$managedTeamIds`
- **الموقع**: `app/Http/Controllers/TaskController.php` - دالة `index`

### 2. **مسارات خاطئة في العرض**
- **المشكلة**: استخدام مسارات خاطئة في `manager/tasks/index.blade.php`
- **الموقع**: `resources/views/manager/tasks/index.blade.php`

## ✅ الحلول المطبقة:

### 1. **إضافة managedTeamIds في المتحكم**
```php
// تحديد الفرق التي يديرها المستخدم
$managedTeamIds = $teams->where('pivot.role', 'manager')->pluck('id')->toArray();

return view('manager.tasks.index', [
    'tasks' => $tasks,
    'teams' => $teams,
    'managedTeamIds' => $managedTeamIds,
]);
```

### 2. **إصلاح المسارات في العرض**
```php
// قبل
<form action="{{ route('tasks.destroy', $task) }}" method="POST">

// بعد
<form action="{{ route('manager.tasks.destroy', $task) }}" method="POST">
```

```php
// قبل
<form action="{{ route('tasks.updateStatus', $task) }}" method="POST">

// بعد
<form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST">
```

```php
// قبل
<a href="{{ route('tasks.create') }}" class="btn btn-success">

// بعد
<a href="{{ route('manager.tasks.create') }}" class="btn btn-success">
```

## 🔧 المنطق المطبق:

### للفرق التي يديرها المدير:
- ✅ **عرض** - يمكن عرض تفاصيل المهمة
- ✅ **تعديل** - يمكن تعديل المهمة
- ✅ **حذف** - يمكن حذف المهمة

### للفرق التي هو عضو فيها فقط:
- ✅ **عرض** - يمكن عرض تفاصيل المهمة
- ✅ **تحديث الحالة** - يمكن تحديث حالة المهمة فقط

## 🎯 اختبار النظام:

1. **تسجيل الدخول** كمدير
2. **الوصول إلى "إدارة المهام"** - يجب أن تعمل بدون أخطاء
3. **التحقق من الأزرار**:
   - للمهام في الفرق التي يديرها: عرض + تعديل + حذف
   - للمهام في الفرق التي هو عضو فيها: عرض + تحديث الحالة فقط
4. **اختبار الفلترة** - حسب الفريق والأولوية والحالة
5. **اختبار إضافة مهمة جديدة** - يجب أن تفتح صفحة الإنشاء

## ✅ النتيجة:
- ✅ تم إصلاح خطأ `Undefined variable $managedTeamIds`
- ✅ جميع المسارات تعمل بشكل صحيح
- ✅ المنطق الصحيح للأذونات مطبق
- ✅ المدير يمكنه إدارة مهام فرقه فقط
- ✅ المدير يمكنه تحديث حالة مهامه كعضو

النظام الآن يعمل بشكل صحيح! 🚀 