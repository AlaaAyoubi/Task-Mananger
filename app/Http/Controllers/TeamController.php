<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = config('constants.pagination.per_page');
        $teams = Team::with('users')->latest()->paginate($perPage);
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $teamRoles = config('constants.team_roles');
        return view('admin.teams.create', compact('users', 'teamRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();
        
        DB::transaction(function () use ($data) {
            $team = Team::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            // تعيين المدير
            $team->users()->attach($data['manager_id'], ['role' => config('constants.team_roles.manager')]);
            
            // إضافة الأعضاء (بدون تكرار المدير)
            $members = collect($data['members'] ?? [])->filter(fn($id) => $id != $data['manager_id']);
            foreach ($members as $memberId) {
                $team->users()->attach($memberId, ['role' => config('constants.team_roles.member')]);
            }
        });
        
        return redirect()->route('teams.index')->with('success', config('constants.success_messages.team.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load('users');
        $teamRoles = config('constants.team_roles');
        return view('admin.teams.show', compact('team', 'teamRoles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $users = User::all();
        $team->load('users');
        $teamRoles = config('constants.team_roles');
        return view('admin.teams.edit', compact('team', 'users', 'teamRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $request->validated();
        
        DB::transaction(function () use ($data, $team) {
            $team->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);
            
            // إعادة تعيين الأعضاء والمدير
            $team->users()->detach();
            $team->users()->attach($data['manager_id'], ['role' => config('constants.team_roles.manager')]);
            
            $members = collect($data['members'] ?? [])->filter(fn($id) => $id != $data['manager_id']);
            foreach ($members as $memberId) {
                $team->users()->attach($memberId, ['role' => config('constants.team_roles.member')]);
            }
        });
        
        return redirect()->route('teams.show', $team)->with('success', config('constants.success_messages.team.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', config('constants.success_messages.team.deleted'));
    }

    /**
     * جلب أعضاء فريق معين (لـ AJAX)
     */
    public function getMembers(Request $request, Team $team)
    {
        $user = $request->user();

        // التحقق من الصلاحية: يجب أن يكون المستخدم إما "أدمن" أو "مدير" لأي فريق
        if (!$user->hasRole('admin') && !$user->teams()->wherePivot('role', 'manager')->exists()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        // جلب الأعضاء باستخدام الطريقة الصحيحة (Eager Loading)
        $members = $team->users;
        
        return response()->json([
            'members' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'role' => $member->pivot->role ?? ''
                ];
            })
        ]);
    }
} 