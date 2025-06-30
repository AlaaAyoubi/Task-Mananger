@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">مهامي</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" class="mb-3 row g-2">
        <div class="col-auto">
            <select name="team_id" class="form-select">
                <option value="">كل الفرق</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ (isset($selectedTeamId) && $selectedTeamId == $team->id) ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
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
            <a href="{{ route('manager.tasks.create') }}" class="btn btn-success">إضافة مهمة جديدة</a>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>الوصف</th>
                <th>الفريق</th>
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
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->team->name ?? '-' }}</td>
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
                        <a href="{{ route('manager.tasks.show', $task) }}" class="btn btn-info btn-sm">عرض</a>
                        <a href="{{ route('manager.tasks.edit', $task) }}" class="btn btn-sm btn-warning">تعديل</a>
                        <form action="{{ route('manager.tasks.destroy', $task) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">لا توجد مهام.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $tasks->appends(request()->query())->links() }}
</div>
@endsection 