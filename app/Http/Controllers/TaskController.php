<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = config('constants.pagination.per_page');

        if ($user->hasRole('admin')) {
            // الأدمن يرى جميع المهام وجميع الفرق
            $tasksQuery = Task::query();
            $teams = Team::all();
            
            // فلترة حسب الأولوية إذا تم تمريرها
            if ($request->filled('priority')) {
                $tasksQuery->where('priority', $request->priority);
            }

            // فلترة حسب الحالة إذا تم تمريرها
            if ($request->filled('status')) {
                $tasksQuery->where('status', $request->status);
            }

            // فلترة حسب الفريق إذا تم تمريرها
            if ($request->filled('team_id')) {
                $tasksQuery->where('team_id', $request->team_id);
            }
            
            $tasks = $tasksQuery->with(['team', 'user'])->latest()->paginate($perPage);
            
            return view('admin.tasks.index', [
                'tasks' => $tasks,
                'teams' => $teams,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        } else {
            // المدير يرى مهام الفرق التي يديرها فقط
            $managedTeams = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->get();
            $managedTeamIds = $managedTeams->pluck('id')->toArray();
            
            $tasksQuery = Task::whereIn('team_id', $managedTeamIds);
            
            // فلترة حسب الأولوية إذا تم تمريرها
            if ($request->filled('priority')) {
                $tasksQuery->where('priority', $request->priority);
            }

            // فلترة حسب الحالة إذا تم تمريرها
            if ($request->filled('status')) {
                $tasksQuery->where('status', $request->status);
            }

            // فلترة حسب الفريق إذا تم تمريرها
            if ($request->filled('team_id')) {
                $tasksQuery->where('team_id', $request->team_id);
            }

            $tasks = $tasksQuery->with(['team', 'user'])->latest()->paginate($perPage);
            
            return view('manager.tasks.index', [
                'tasks' => $tasks,
                'teams' => $managedTeams,
                'managedTeamIds' => $managedTeamIds,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            // الأدمن يرى جميع الفرق
            $teams = Team::all();
            
            return view('admin.tasks.create', [
                'teams' => $teams,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        } else {
            // المدير يرى فقط الفرق التي يديرها
            $managedTeams = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->get();
            
            // جلب جميع أعضاء الفرق التي يديرها المستخدم
            $members = collect();
            foreach ($managedTeams as $team) {
                $members = $members->merge($team->users);
            }
            $members = $members->unique('id');
            
            return view('manager.tasks.create', [
                'teams' => $managedTeams,
                'members' => $members,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'] ?? null,
            'team_id' => $data['team_id'],
            'user_id' => $data['assigned_user_id'],
        ]);

        // إرسال إشعار عند إنشاء المهمة
        NotificationService::taskCreated($task, $request->user());

        // إعادة التوجيه حسب الدور
        $user = $request->user();
        if ($user->hasRole('admin')) {
            return redirect()->route('tasks.index')->with('success', config('constants.success_messages.task.created'));
        } else {
            return redirect()->route('manager.tasks.index')->with('success', config('constants.success_messages.task.created'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            // مسموح للأدمن
        } else {
            // تحقق من الفرق التي يديرها في الـpivot
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            if (!in_array($task->team_id, $managedTeamIds)) {
                abort(403, config('constants.error_messages.forbidden'));
            }
        }
        
        $task->load(['team', 'user']);
        
        if ($user->hasRole('admin')) {
            return view('admin.tasks.show', [
                'task' => $task,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        } else {
            // تحديد ما إذا كان المدير يدير فريق هذه المهمة
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            $isManagerOfTeam = in_array($task->team_id, $managedTeamIds);
            
            return view('manager.tasks.show', [
                'task' => $task,
                'isManagerOfTeam' => $isManagerOfTeam,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task)
    {
        $user = $request->user();
        Log::info('ENTERED edit', [
            'user_id' => $user->id,
            'roles' => $user->roles ?? null,
            'manager_teams' => $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray(),
            'task_team_id' => $task->team_id,
        ]);
        
        if ($user->hasRole('admin')) {
            // مسموح للأدمن
        } else {
            // تحقق من الفرق التي يديرها في الـpivot
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            if (!in_array($task->team_id, $managedTeamIds)) {
                abort(403, config('constants.error_messages.forbidden'));
            }
        }
        
        $team = $task->team;
        
        if ($user->hasRole('admin')) {
            return view('admin.tasks.edit', [
                'task' => $task,
                'team' => $team,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        } else {
            // المدير يرى أعضاء الفريق فقط
            $members = $team->users;
            
            return view('manager.tasks.edit', [
                'task' => $task,
                'team' => $team,
                'members' => $members,
                'statuses' => config('constants.task_statuses'),
                'priorities' => config('constants.task_priorities'),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $user = $request->user();
        Log::info('ENTERED update', [
            'user_id' => $user->id,
            'roles' => $user->roles ?? null,
            'manager_teams' => $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray(),
            'task_team_id' => $task->team_id,
        ]);
        
        if ($user->hasRole('admin')) {
            // مسموح للأدمن
        } else {
            // تحقق من الفرق التي يديرها في الـpivot
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            if (!in_array($task->team_id, $managedTeamIds)) {
                abort(403, config('constants.error_messages.forbidden'));
            }
        }

        // حفظ البيانات القديمة للمقارنة
        $oldData = $task->toArray();
        
        $data = $request->validated();
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
            'priority' => $data['priority'] ?? $task->priority,
            'due_date' => $data['due_date'] ?? $task->due_date,
            'team_id' => $data['team_id'] ?? $task->team_id,
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
            NotificationService::taskUpdated($task, $request->user(), $changes);
        }

        // إعادة التوجيه حسب الدور
        if ($user->hasRole('admin')) {
            return redirect()->route('tasks.index')->with('success', config('constants.success_messages.task.updated'));
        } else {
            return redirect()->route('manager.tasks.index')->with('success', config('constants.success_messages.task.updated'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            // مسموح للأدمن
        } else {
            // تحقق من الفرق التي يديرها في الـpivot
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            if (!in_array($task->team_id, $managedTeamIds)) {
                abort(403, config('constants.error_messages.forbidden'));
            }
        }
        
        $task->delete();
        
        if ($user->hasRole('admin')) {
            return redirect()->route('tasks.index')->with('success', config('constants.success_messages.task.deleted'));
        } else {
            return redirect()->route('manager.tasks.index')->with('success', config('constants.success_messages.task.deleted'));
        }
    }

    // =============================
    // للعضو
    // =============================

    /**
     * عرض المهام الخاصة بالعضو فقط
     */
    public function myTasks(Request $request)
    {
        $user = $request->user();
        $perPage = config('constants.pagination.per_page');
        
        $tasksQuery = $user->tasks();
        
        // فلترة حسب الأولوية إذا تم تمريرها
        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        // فلترة حسب الحالة إذا تم تمريرها
        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        // فلترة حسب الفريق إذا تم تمريرها
        if ($request->filled('team_id')) {
            $tasksQuery->where('team_id', $request->team_id);
        }
        
        $tasks = $tasksQuery->with(['team'])->latest()->paginate($perPage);
        
        // جلب الفرق التي ينتمي إليها المستخدم
        $teams = $user->teams;
        
        return view('member.tasks.index', [
            'tasks' => $tasks,
            'teams' => $teams,
            'statuses' => config('constants.task_statuses'),
            'priorities' => config('constants.task_priorities'),
        ]);
    }

    /**
     * عرض مهام المدير كعضو (فقط تعديل الحالة)
     */
    public function managerMyTasks(Request $request)
    {
        $user = $request->user();
        $perPage = config('constants.pagination.per_page');
        
        $tasksQuery = $user->tasks();
        
        // فلترة حسب الأولوية إذا تم تمريرها
        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        // فلترة حسب الحالة إذا تم تمريرها
        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        // فلترة حسب الفريق إذا تم تمريرها
        if ($request->filled('team_id')) {
            $tasksQuery->where('team_id', $request->team_id);
        }
        
        $tasks = $tasksQuery->with(['team'])->latest()->paginate($perPage);
        
        // جلب الفرق التي يديرها المستخدم
        $managedTeams = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->get();
        
        return view('manager.tasks.my-tasks', [
            'tasks' => $tasks,
            'teams' => $managedTeams,
            'statuses' => config('constants.task_statuses'),
            'priorities' => config('constants.task_priorities'),
        ]);
    }

    /**
     * تحديث حالة مهمة خاصة بالعضو
     */
    public function updateStatus(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // مسموح للأدمن
        } elseif ($task->user_id === $user->id) {
            // مسموح للمستخدم تحديث حالة مهامه الخاصة
        } else {
            // تحقق من الفرق التي يديرها في الـpivot
            $managedTeamIds = $user->teams()->wherePivot('role', config('constants.team_roles.manager'))->pluck('teams.id')->toArray();
            if (!in_array($task->team_id, $managedTeamIds)) {
                abort(403, config('constants.error_messages.forbidden'));
            }
        }

        $oldStatus = $task->status;
        $task->update(['status' => $data['status']]);
        
        // إرسال إشعار عند تحديث حالة المهمة
        NotificationService::taskStatusChanged($task, $request->user(), $oldStatus, $data['status']);

        return response()->json([
            'message' => config('constants.success_messages.task.status_updated'),
            'status' => $data['status'],
            'status_label' => config('constants.task_statuses')[$data['status']] ?? $data['status']
        ]);
    }
}
