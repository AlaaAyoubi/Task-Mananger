<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\NotificationService;

class ManagerTeamController extends Controller
{
    // عرض جميع الفرق التي يديرها المدير الحالي
    public function index(Request $request)
    {
        $user = $request->user();
        $teams = $user->teams()->wherePivot('role', 'manager')->with('users')->latest()->paginate(10);
        return view('manager.teams.index', compact('teams'));
    }

    // عرض تفاصيل فريق
    public function show(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }
        $team->load('users');
        
        // جلب مهام الفريق
        $tasks = $team->tasks()->with('user')->latest()->paginate(10);
        
        return view('manager.teams.show', compact('team', 'tasks'));
    }

    // عرض نموذج تعديل فريق
    public function edit(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }
        $users = User::all();
        $team->load('users');
        return view('manager.teams.edit', compact('team', 'users'));
    }

    // تحديث بيانات فريق
    public function update(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'array',
            'members.*' => 'exists:users,id',
        ]);
        
        DB::transaction(function () use ($request, $team) {
            $team->update(['name' => $request->name]);
            
            // الحصول على المدير الحالي
            $currentManager = $team->users()->where('role', 'manager')->first();
            
            // إعادة تعيين الأعضاء فقط (بدون تغيير المدير)
            $team->users()->detach();
            
            // إعادة إضافة المدير الحالي
            if ($currentManager) {
                $team->users()->attach($currentManager->id, ['role' => 'manager']);
            }
            
            // إضافة الأعضاء الجدد
            if ($request->has('members')) {
                $members = collect($request->members)->filter(fn($id) => $id != $currentManager->id);
                foreach ($members as $memberId) {
                    $team->users()->attach($memberId, ['role' => 'member']);
                }
            }
        });
        
        return redirect()->route('manager.teams.show', $team)->with('success', 'تم تحديث بيانات الفريق بنجاح!');
    }

    // حذف فريق
    public function destroy(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }
        $team->delete();
        return redirect()->route('manager.teams')->with('success', 'تم حذف الفريق بنجاح!');
    }

    // =============================
    // إدارة مهام الفرق
    // =============================

    // عرض مهام فريق معين
    public function teamTasks(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }

        $tasksQuery = $team->tasks()->with(['user']);

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        $tasks = $tasksQuery->latest()->paginate(10);
        $members = $team->users;

        return view('manager.teams.tasks.index', [
            'team' => $team,
            'tasks' => $tasks,
            'members' => $members,
        ]);
    }

    // عرض نموذج إضافة مهمة جديدة لفريق
    public function createTask(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }

        $members = $team->users;
        return view('manager.teams.tasks.create', [
            'team' => $team,
            'members' => $members,
        ]);
    }

    // حفظ مهمة جديدة لفريق
    public function storeTask(StoreTaskRequest $request, Team $team)
    {
        // التحقق من أن المستخدم مدير لهذا الفريق
        $user = $request->user();
        $pivot = $team->users()->where('user_id', $user->id)->first();
        if (!$pivot || $pivot->role !== 'manager') {
            abort(403, 'غير مصرح لك بإضافة مهام لهذا الفريق.');
        }

        $data = $request->validated();
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'] ?? null,
            'team_id' => $team->id,
            'user_id' => $data['assigned_user_id'],
        ]);

        // إرسال إشعار عند إنشاء المهمة
        NotificationService::taskCreated($task, $user);

        return redirect()->route('manager.teams.tasks', $team)->with('success', 'تمت إضافة المهمة بنجاح.');
    }

    // عرض نموذج تعديل مهمة
    public function editTask(Request $request, Team $team, Task $task)
    {
        $user = $request->user();
        $pivot = $team->users()->where('user_id', $user->id)->first();
        if (!$pivot || $pivot->role !== 'manager') {
            abort(403, 'غير مصرح لك بتعديل مهام لهذا الفريق.');
        }

        $members = $team->users;
        return view('manager.teams.tasks.edit', [
            'team' => $team,
            'task' => $task,
            'members' => $members,
        ]);
    }

    // // عرض نموذج تعديل مهمة
    // public function editTask(Request $request, Team $team, Task $task)
    // {
    //     $user = $request->user();
    //     if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
    //          abort(403);
    //     }

    //     if ($task->team_id !== $team->id) {
    //          abort(403);
    //     }

    //     $members = $team->users;
    //     return view('manager.teams.tasks.edit', [
    //         'team' => $team,
    //         'task' => $task,
    //         'members' => $members,
    //     ]);
    // }

    // تحديث مهمة
    public function updateTask(UpdateTaskRequest $request, Team $team, Task $task)
    {

        $user = $request->user();
        $pivot = $team->users()->where('user_id', $user->id)->first();
        if (!$pivot || $pivot->role !== 'manager') {
            abort(403, 'غير مصرح لك بتعديل مهام لهذا الفريق.');
        }
        // // التحقق من أن المستخدم مدير لهذا الفريق
        // $user = $request->user();
        // $pivot = $team->users()->where('user_id', $user->id)->first();
        // if (!$pivot || $pivot->role !== 'manager') {
        //     abort(403, 'غير مصرح لك بتعديل مهام هذا الفريق.');
        // }

        // // التحقق من أن المهمة تخص هذا الفريق
        // if ($task->team_id !== $team->id) {
        //     abort(404, 'المهمة غير موجودة في هذا الفريق.');
        // }

        // حفظ البيانات القديمة للمقارنة
        $oldData = $task->toArray();
        
        $data = $request->validated();
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
            'priority' => $data['priority'] ?? $task->priority,
            'due_date' => $data['due_date'] ?? $task->due_date,
            'user_id' => $data['assigned_user_id'] ?? $task->user_id,
        ]);

        // تحديد التغييرات
        $changes = [];
        foreach ($data as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }

        // إرسال إشعار عند تحديث المهمة
        if (!empty($changes)) {
            NotificationService::taskUpdated($task, $user, $changes);
        }

        return redirect()->route('manager.teams.tasks', $team)->with('success', 'تم تحديث المهمة بنجاح.');
    }

    // حذف مهمة
    public function destroyTask(Request $request, Team $team, Task $task)
    {
        $user = $request->user();
        if (!$team->users()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403);
        }

        if ($task->team_id !== $team->id) {
            abort(403);
        }

        $task->delete();
        return redirect()->route('manager.teams.tasks', $team)->with('success', 'تم حذف المهمة بنجاح.');
    }
} 