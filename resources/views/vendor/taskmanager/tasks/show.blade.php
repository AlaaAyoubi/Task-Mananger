@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">تفاصيل المهمة: {{ $task->title }}</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">العنوان: {{ $task->title }}</h5>
            <p class="card-text"><strong>الوصف:</strong> {{ $task->description }}</p>
            <p class="card-text"><strong>الفريق:</strong> {{ $task->team->name ?? '-' }}</p>
            <p class="card-text"><strong>المكلف:</strong> {{ $task->user->name ?? '-' }}</p>
            <p class="card-text"><strong>الحالة:</strong> 
                <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'warning' : ($task->status == 'canceled' ? 'danger' : 'secondary')) }}">
                    {{ $task->status == 'pending' ? 'قيد الانتظار' : ($task->status == 'in_progress' ? 'قيد التنفيذ' : ($task->status == 'completed' ? 'مكتملة' : 'ملغاة')) }}
                </span>
            </p>
            <p class="card-text"><strong>الأولوية:</strong> 
                <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                    {{ $task->priority == 'high' ? 'عالية' : ($task->priority == 'medium' ? 'متوسطة' : 'منخفضة') }}
                </span>
            </p>
            <p class="card-text"><strong>تاريخ التسليم:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '-' }}</p>
            <div class="mt-3">
                @if($isManagerOfTeam)
                    <a href="{{ route('manager.tasks.edit', $task) }}" class="btn btn-warning">تعديل</a>
                    <form action="{{ route('manager.tasks.destroy', $task) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                    </form>
                @else
                    <form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select form-select-sm d-inline w-auto">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="canceled" {{ $task->status == 'canceled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                        <button type="submit" class="btn btn-success">تحديث الحالة</button>
                    </form>
                @endif
                <a href="{{ route('manager.tasks.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>
        </div>
    </div>
</div>
@endsection 