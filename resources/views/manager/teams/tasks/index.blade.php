@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>مهام فريق: {{ $team->name }}</h2>
        <a href="{{ route('manager.teams') }}" class="btn btn-secondary">العودة للفرق</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- فلترة المهام -->
    <form method="GET" class="mb-3 row g-2">
        <div class="col-auto">
            <select name="priority" class="form-select">
                <option value="">كل الأولويات</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>ملغاة</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">فلترة</button>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('manager.teams.tasks.create', $team) }}" class="btn btn-success">إضافة مهمة جديدة</a>
        </div>
    </form>

    <!-- جدول المهام -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>الوصف</th>
                        <th>المكلف</th>
                        <th>الحالة</th>
                        <th>الأولوية</th>
                        <th>تاريخ التسليم</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ Str::limit($task->description, 50) }}</td>
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
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manager.teams.tasks.edit', [$team, $task]) }}" class="btn btn-sm btn-warning">تعديل</a>
                                    <form action="{{ route('manager.teams.tasks.destroy', [$team, $task]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد مهام لهذا الفريق.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ترقيم الصفحات -->
    <div class="mt-3">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
</div>
@endsection 