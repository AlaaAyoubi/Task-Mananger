@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل الفريق: {{ $team->name }}</h2>
        <div>
            <a href="{{ route('manager.teams.tasks', $team) }}" class="btn btn-primary">إدارة مهام الفريق</a>
            <a href="{{ route('manager.teams.edit', $team) }}" class="btn btn-warning">تعديل الفريق</a>
            <a href="{{ route('manager.teams') }}" class="btn btn-secondary">العودة للفرق</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- معلومات الفريق -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>معلومات الفريق</h5>
                </div>
                <div class="card-body">
                    <p><strong>اسم الفريق:</strong> {{ $team->name }}</p>
                    <p><strong>تاريخ الإنشاء:</strong> {{ $team->created_at->format('Y-m-d') }}</p>
                    <p><strong>عدد الأعضاء:</strong> {{ $team->users->count() }}</p>
                    <p><strong>عدد المهام:</strong> {{ $team->tasks->count() }}</p>
                </div>
            </div>
        </div>

        <!-- أعضاء الفريق -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>أعضاء الفريق</h5>
                </div>
                <div class="card-body">
                    @if($team->users->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($team->users as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $user->name }}
                                    <span class="badge bg-{{ $user->pivot->role == 'manager' ? 'primary' : 'secondary' }}">
                                        {{ $user->pivot->role == 'manager' ? 'مدير' : 'عضو' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">لا يوجد أعضاء في هذا الفريق.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- المهام الأخيرة -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>المهام الأخيرة</h5>
            <a href="{{ route('manager.teams.tasks', $team) }}" class="btn btn-sm btn-primary">عرض جميع المهام</a>
        </div>
        <div class="card-body">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>المكلف</th>
                                <th>الحالة</th>
                                <th>الأولوية</th>
                                <th>تاريخ التسليم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks->take(5) as $task)
                                <tr>
                                    <td>{{ Str::limit($task->title, 30) }}</td>
                                    <td>{{ $task->user->name ?? 'غير محدد' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'warning' : ($task->status == 'canceled' ? 'danger' : 'secondary')) }}">
                                            {{ $task->status == 'pending' ? 'قيد الانتظار' : ($task->status == 'in_progress' ? 'قيد التنفيذ' : ($task->status == 'completed' ? 'مكتملة' : 'ملغاة')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                                            {{ $task->priority == 'high' ? 'عالية' : ($task->priority == 'medium' ? 'متوسطة' : 'منخفضة') }}
                                        </span>
                                    </td>
                                    <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">لا توجد مهام لهذا الفريق.</p>
            @endif
        </div>
    </div>
</div>
@endsection 