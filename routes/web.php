<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Team;
use Illuminate\Http\Request;

//  الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
});

//  إعادة التوجيه حسب الدور بعد تسجيل الدخول
Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    } elseif ($user->hasRole('member')) {
        return redirect()->route('member.dashboard');
    }

    abort(403, 'لم يتم تحديد صلاحية مناسبة.');
})->middleware(['auth'])->name('dashboard');

//  لوحات التحكم حسب الدور
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'team.role:manager'])->group(function () {
    Route::get('/manager/dashboard', function () {
        return view('manager.dashboard');
    })->name('manager.dashboard');
    
    // مسار بديل للوحة تحكم المدير
    Route::get('/manager', function () {
        return view('manager.dashboard');
    })->name('manager.home');
});

Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/member/dashboard', function () {
        return view('member.dashboard');
    })->name('member.dashboard');
});

// =============================
// مسارات إدارة المهام (Tasks)
// =============================

// للأدمن: CRUD كامل على المهام
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
});

// للمدير: CRUD كامل على مهام فرقه
Route::middleware(['auth', 'team.role:manager'])->group(function () {
    // قائمة المهام
    Route::get('manager/tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('manager.tasks.index');
    // إضافة مهمة
    Route::get('manager/tasks/create', [\App\Http\Controllers\TaskController::class, 'create'])->name('manager.tasks.create');
    Route::post('manager/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('manager.tasks.store');
    // عرض مهمة
    Route::get('manager/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('manager.tasks.show');
    // تعديل مهمة
    Route::get('manager/tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('manager.tasks.edit');
    Route::put('manager/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('manager.tasks.update');
    // حذف مهمة
    Route::delete('manager/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('manager.tasks.destroy');
    // مهامي كعضو
    Route::get('manager/my-tasks', [\App\Http\Controllers\TaskController::class, 'managerMyTasks'])->name('manager.my-tasks');
    Route::patch('manager/my-tasks/{task}', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('manager.my-tasks.updateStatus');
});

// للعضو: عرض مهامه فقط وتعديل حالتها
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks'])->name('tasks.my');
    Route::patch('my-tasks/{task}', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
});

//  إعدادات الحساب
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//  مصادقة المستخدمين
require __DIR__.'/auth.php';

// =============================
// مسارات إدارة الفرق (Teams)
// =============================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('teams', \App\Http\Controllers\TeamController::class);
});

// =============================
// مسارات فرق المدير
// =============================
Route::middleware(['auth', 'team.role:manager'])->group(function () {
    Route::get('manager/teams', [\App\Http\Controllers\ManagerTeamController::class, 'index'])->name('manager.teams');
    Route::get('manager/teams/{team}', [\App\Http\Controllers\ManagerTeamController::class, 'show'])->name('manager.teams.show');
    Route::get('manager/teams/{team}/edit', [\App\Http\Controllers\ManagerTeamController::class, 'edit'])->name('manager.teams.edit');
    Route::put('manager/teams/{team}', [\App\Http\Controllers\ManagerTeamController::class, 'update'])->name('manager.teams.update');
    Route::delete('manager/teams/{team}', [\App\Http\Controllers\ManagerTeamController::class, 'destroy'])->name('manager.teams.destroy');
    
    // مسارات إدارة مهام الفرق
    Route::get('manager/teams/{team}/tasks', [\App\Http\Controllers\ManagerTeamController::class, 'teamTasks'])->name('manager.teams.tasks');
    Route::get('manager/teams/{team}/tasks/create', [\App\Http\Controllers\ManagerTeamController::class, 'createTask'])->name('manager.teams.tasks.create');
    Route::post('manager/teams/{team}/tasks', [\App\Http\Controllers\ManagerTeamController::class, 'storeTask'])->name('manager.teams.tasks.store');
    Route::get('manager/teams/{team}/tasks/{task}/edit', [\App\Http\Controllers\ManagerTeamController::class, 'editTask'])->name('manager.teams.tasks.edit');
    Route::put('manager/teams/{team}/tasks/{task}', [\App\Http\Controllers\ManagerTeamController::class, 'updateTask'])->name('manager.teams.tasks.update');
    Route::delete('manager/teams/{team}/tasks/{task}', [\App\Http\Controllers\ManagerTeamController::class, 'destroyTask'])->name('manager.teams.tasks.destroy');
});

// =============================
// مسارات الإشعارات
// =============================
Route::middleware('auth')->group(function () {
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::patch('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [\App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    
    // مسارات AJAX
    Route::get('notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::get('notifications/latest', [\App\Http\Controllers\NotificationController::class, 'latest'])->name('notifications.latest');
    
    // مسار لجلب أعضاء فريق معين
    Route::get('teams/{team}/members', [\App\Http\Controllers\TeamController::class, 'getMembers'])->name('teams.members');
});
