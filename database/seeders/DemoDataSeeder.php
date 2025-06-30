<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الأدمن العام
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(config('constants.roles.admin'));

        // 2. إنشاء فريقين
        $team1 = Team::create(['name' => 'فريق التطوير']);
        $team2 = Team::create(['name' => 'فريق التسويق']);

        // 3. إنشاء 6 مستخدمين
        $users = User::factory()->count(6)->create();

        // 4. توزيع الأدوار وربطهم بالفرق
        foreach ($users as $index => $user) {
            // تعيين الدور العام للمستخدم
            if ($index < 2) {
                $user->assignRole(config('constants.roles.manager'));
            } else {
                $user->assignRole(config('constants.roles.member'));
            }

            // ربط المستخدم بفريقه ودوره داخل الفريق
            $team = $index < 3 ? $team1 : $team2;
            $roleInTeam = $index % 3 == 0 ? 'manager' : 'member';

            $team->users()->attach($user->id, ['role' => $roleInTeam]);
        }

        // 5. إنشاء مهام وتوزيعها على أعضاء الفريقين
        $allUsers = $team1->users->merge($team2->users);

        foreach ($allUsers as $user) {
            Task::create([
                'title' => 'مهمة تجريبية لـ ' . $user->name,
                'description' => 'هذا وصف مهمة تخص المستخدم.',
                'team_id' => $user->teams->first()->id,
                'user_id' => $user->id,
                'status' => config('constants.task_statuses.pending'),
                'priority' => 'medium',
            ]);
        }

        // 6. إنشاء إشعارات تجريبية
        $tasks = Task::all();
        foreach ($tasks as $task) {
            // إشعار إنشاء مهمة للمدير والأدمن
            $managersAndAdmins = User::whereHas('teams', function ($query) use ($task) {
                $query->where('team_id', $task->team_id)
                      ->whereIn('role', ['manager', 'admin']);
            })->orWhereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->get();

            foreach ($managersAndAdmins as $manager) {
                if ($manager->id !== $task->user_id) {
                    Notification::create([
                        'user_id' => $manager->id,
                        'type' => 'task_created',
                        'message' => 'تم إنشاء مهمة جديدة "' . $task->title . '" بواسطة النظام',
                        'data' => [
                            'task_id' => $task->id,
                            'task_title' => $task->title,
                            'created_by' => 'النظام',
                            'team_id' => $task->team_id,
                        ],
                        'is_read' => false,
                    ]);
                }
            }

            // إشعار للعضو المكلف بالمهمة
            if ($task->user_id) {
                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'task_created',
                    'message' => 'تم تعيين مهمة جديدة لك: "' . $task->title . '"',
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'created_by' => 'النظام',
                        'team_id' => $task->team_id,
                    ],
                    'is_read' => false,
                ]);
            }
        }
    }
}
