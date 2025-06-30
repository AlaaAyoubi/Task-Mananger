<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class ManagerTaskController extends Controller
{
    // عرض جميع المهام المكلف بها المدير
    public function index(Request $request)
    {
        $user = $request->user();
        // جلب معرفات الفرق التي هو مديرها
        $managedTeamIds = $user->teams()->wherePivot('role', 'manager')->pluck('teams.id')->toArray();
        // جلب جميع الفرق التي ينتمي إليها المدير (للفلترة)
        $allTeamIds = $user->teams->pluck('id');
        $teams = $user->teams;

        // جميع المهام المكلف بها المدير
        $tasksQuery = Task::with(['team', 'user'])
            ->where('user_id', $user->id);
        if ($request->filled('team_id')) {
            $tasksQuery->where('team_id', $request->team_id);
        }
        $tasks = $tasksQuery->latest()->paginate(10);

        return view('manager.tasks.index', [
            'tasks' => $tasks,
            'managedTeamIds' => $managedTeamIds,
            'teams' => $teams,
            'selectedTeamId' => $request->team_id,
        ]);
    }

    // عرض تفاصيل مهمة واحدة
    public function show(Request $request, Task $task)
    {
        $user = $request->user();
        // يجب أن يكون المدير هو المكلف بالمهمة
        if ($task->user_id !== $user->id) {
            abort(403);
        }
        $managedTeamIds = $user->teams()->wherePivot('role', 'manager')->pluck('teams.id')->toArray();
        return view('manager.tasks.show', [
            'task' => $task,
            'isManagerOfTeam' => in_array($task->team_id, $managedTeamIds),
        ]);
    }
} 