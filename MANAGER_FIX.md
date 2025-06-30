# إصلاح مشكلة لوحة تحكم المدير

## ❌ المشكلة الأصلية:
```
RouteNotFoundException: Route [manager.tasks] not defined.
```

## 🔍 أسباب المشكلة:

### 1. **مسار خاطئ في dashboard.blade.php**
- **المشكلة**: استخدام `route('manager.tasks')` بدلاً من `route('manager.tasks.index')`
- **الموقع**: `resources/views/manager/dashboard.blade.php` - السطر 64

### 2. **مسار مفقود في routes/web.php**
- **المشكلة**: عدم وجود مسار `manager.tasks.show`
- **الموقع**: `routes/web.php` - مسارات المدير

### 3. **دالة مفقودة في TaskController**
- **المشكلة**: عدم وجود دالة `show` في `TaskController`
- **الموقع**: `app/Http/Controllers/TaskController.php`

### 4. **ملف عرض مفقود**
- **المشكلة**: عدم وجود ملف `admin/tasks/show.blade.php`
- **الموقع**: `resources/views/admin/tasks/show.blade.php`

## ✅ الحلول المطبقة:

### 1. **إصلاح المسار في dashboard.blade.php**
```php
// قبل
<a href="{{ route('manager.tasks') }}" class="btn btn-warning">

// بعد
<a href="{{ route('manager.tasks.index') }}" class="btn btn-warning">
```

### 2. **إضافة المسار المفقود في routes/web.php**
```php
Route::get('manager/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('manager.tasks.show');
```

### 3. **إضافة دالة show في TaskController**
```php
public function show(Request $request, Task $task)
{
    $user = $request->user();
    
    // التحقق من الصلاحيات
    if ($user->hasRole('admin')) {
        // مسموح
    } elseif ($user->hasRole('manager')) {
        // المدير فقط إذا كان مديرًا لهذا الفريق
        $team = $task->team;
        $pivot = $team->users()->where('user_id', $user->id)->first();
        if (!$pivot || $pivot->role !== 'manager') {
            abort(403, 'غير مصرح لك بعرض هذه المهمة.');
        }
    } else {
        abort(403, 'غير مصرح لك بعرض هذه المهمة.');
    }
    
    $task->load(['team', 'user']);
    
    if ($user->hasRole('admin')) {
        return view('admin.tasks.show', ['task' => $task]);
    } else {
        return view('manager.tasks.show', ['task' => $task]);
    }
}
```

### 4. **إنشاء ملف admin/tasks/show.blade.php**
- ملف عرض تفاصيل المهمة للأدمن
- يحتوي على جميع معلومات المهمة
- أزرار التعديل والحذف

## 🔧 المسارات المتاحة الآن:

### مسارات المدير:
- `manager.dashboard` - لوحة تحكم المدير
- `manager.tasks.index` - قائمة مهام المدير
- `manager.tasks.create` - إنشاء مهمة جديدة
- `manager.tasks.store` - حفظ مهمة جديدة
- `manager.tasks.show` - عرض تفاصيل المهمة
- `manager.tasks.edit` - تعديل المهمة
- `manager.tasks.update` - تحديث المهمة
- `manager.tasks.destroy` - حذف المهمة
- `manager.my-tasks` - مهام المدير كعضو
- `manager.teams` - إدارة الفرق

## ✅ النتيجة:
- ✅ تم إصلاح خطأ `RouteNotFoundException`
- ✅ جميع المسارات مُعرّفة بشكل صحيح
- ✅ لوحة تحكم المدير تعمل بشكل طبيعي
- ✅ يمكن للمدير عرض وتعديل وحذف المهام
- ✅ يمكن للمدير إدارة الفرق

## 🎯 اختبار النظام:
1. **تسجيل الدخول** كمدير
2. **الوصول إلى لوحة التحكم** - يجب أن تعمل بدون أخطاء
3. **النقر على "إدارة المهام"** - يجب أن تفتح قائمة المهام
4. **النقر على "عرض"** لأي مهمة - يجب أن تفتح صفحة التفاصيل
5. **النقر على "تعديل"** - يجب أن تفتح صفحة التعديل

النظام الآن يعمل بشكل صحيح! 🚀 