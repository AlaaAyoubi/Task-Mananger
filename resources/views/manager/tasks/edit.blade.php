@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">تعديل مهمة</h2>
    <form action="{{ route('manager.tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $task->title) }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $task->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="team_id" class="form-label">الفريق</label>
            <select name="team_id" id="team_id" class="form-select" required>
                @foreach($team ? [$team] : [] as $t)
                    <option value="{{ $t->id }}" selected>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="assigned_user_id" class="form-label">المكلف</label>
            <select name="assigned_user_id" id="assigned_user_id" class="form-select" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ $task->user_id == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                <option value="canceled" {{ $task->status == 'canceled' ? 'selected' : '' }}>ملغاة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="priority" class="form-label">الأولوية</label>
            <select name="priority" id="priority" class="form-select" required>
                <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>عالية</option>
                <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>منخفضة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label">تاريخ التسليم</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $task->due_date) }}">
        </div>
        <button type="submit" class="btn btn-primary">تحديث المهمة</button>
        <a href="{{ route('manager.tasks.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection 