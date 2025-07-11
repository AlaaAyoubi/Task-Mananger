# إصلاح صلاحيات المدير - Manager Permissions Fix

## المشاكل التي تم حلها

### 1. مشكلة عرض المهام غير المصرح بها
**المشكلة**: المدير كان يرى مهام لا تخص الفرق التي يديرها، بما في ذلك:
- مهام موكلة إليه شخصياً ولكنها تتبع لفرق أخرى لا يديرها
- مهام تخص أعضاء آخرين في فرق أخرى لا يديرها

**الحل**: تم تعديل دالة `index` في `TaskController` لتجلب فقط مهام الفرق التي يديرها المدير:

```php
// قبل الإصلاح
$teams = $user->teams; // جميع الفرق التي ينتمي إليها
$teamIds = $teams->pluck('id');

// بعد الإصلاح
$managedTeams = $user->teams()->wherePivot('role', 'manager')->get();
$managedTeamIds = $managedTeams->pluck('id')->toArray();
$tasksQuery = Task::whereIn('team_id', $managedTeamIds);
```

### 2. مشكلة صلاحيات تعديل المهام
**المشكلة**: المدير كان يحصل على رسالة "غير مصرح لك" عند محاولة تعديل مهام فريقه رغم أنه مدير الفريق.

**الحل**: تم تعديل منطق التحقق من الصلاحيات في دوال `edit`, `show`, `update`, و `destroy`:

```php
// قبل الإصلاح
$team = $task->team;
$pivot = $team->users()->where('user_id', $user->id)->first();
if (!$pivot || $pivot->role !== 'manager') {
    abort(403, 'غير مصرح لك بتعديل هذه المهمة.');
}

// بعد الإصلاح
$managedTeamIds = $user->teams()->wherePivot('role', 'manager')->pluck('teams.id')->toArray();
if (!in_array($task->team_id, $managedTeamIds)) {
    abort(403, 'غير مصرح لك بتعديل هذه المهمة.');
}
```

## التغييرات المطبقة

### في TaskController.php

1. **دالة `index`**:
   - تعديل استعلام جلب المهام للمدير ليشمل فقط مهام الفرق التي يديرها
   - تعديل جلب الفرق ليشمل فقط الفرق التي يديرها

2. **دالة `create`**:
   - تعديل جلب الفرق ليشمل فقط الفرق التي يديرها المدير
   - تعديل جلب الأعضاء ليشمل فقط أعضاء الفرق التي يديرها

3. **دوال `edit`, `show`, `update`, `destroy`**:
   - تغيير منطق التحقق من الصلاحيات للتحقق من أن المهمة تنتمي لفريق يديره المدير
   - إضافة إعادة توجيه صحيحة حسب الدور

## نتائج الاختبار

تم إجراء اختبار شامل وظهرت النتائج التالية:

```
=== اختبار إصلاحات المدير ===

1. اختبار المدير يرى فقط مهام الفرق التي يديرها:
   المدير: Electa Kuhn
   الفرق التي يديرها: فريق التسويق
   جميع الفرق التي ينتمي إليها: فريق التطوير+, فريق التسويق
   عدد مهام الفرق التي يديرها: 3
   عدد جميع مهام الفرق التي ينتمي إليها: 6
   ✅ المدير يرى فقط مهام الفرق التي يديرها

2. اختبار صلاحيات المدير على المهام:
   مهمة اختبار: مهمة تجريبية لـ Electa Kuhn (فريق: فريق التسويق)
   ✅ المهمة تنتمي لفريق يديره المدير
```

## المزايا المحققة

1. **أمان محسن**: المدير لا يرى مهام فرق أخرى لا يديرها
2. **واجهة مستخدم أوضح**: المدير يرى فقط المهام ذات الصلة بفريقه
3. **صلاحيات صحيحة**: المدير يمكنه تعديل وحذف مهام فريقه
4. **فصل المسؤوليات**: كل مدير مسؤول فقط عن مهام فريقه

## التأكد من الإصلاح

للتأكد من أن الإصلاحات تعمل بشكل صحيح:

1. سجل دخول كمدير
2. انتقل إلى "إدارة المهام"
3. تأكد من أنك ترى فقط مهام الفرق التي تديرها
4. جرب تعديل أو حذف مهمة من فريقه
5. تأكد من أنك لا تستطيع الوصول لمهام فرق أخرى

## ملاحظات مهمة

- الإصلاحات تحافظ على صلاحيات الأدمن الكاملة
- الإصلاحات لا تؤثر على صلاحيات الأعضاء العاديين
- تم مسح الكاش للتأكد من تطبيق التغييرات 