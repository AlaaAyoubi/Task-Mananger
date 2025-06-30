@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">إضافة مهمة جديدة</h2>
    <form action="{{ route('manager.tasks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" name="title" id="title" class="form-control" required value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="team_id" class="form-label">الفريق</label>
            <select name="team_id" id="team_id" class="form-select" required>
                <option value="">اختر الفريق</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="assigned_user_id" class="form-label">المكلف</label>
            <select name="assigned_user_id" id="assigned_user_id" class="form-select" required>
                <option value="">اختر العضو</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('assigned_user_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending">قيد الانتظار</option>
                <option value="in_progress">قيد التنفيذ</option>
                <option value="completed">مكتملة</option>
                <option value="canceled">ملغاة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="priority" class="form-label">الأولوية</label>
            <select name="priority" id="priority" class="form-select" required>
                <option value="high">عالية</option>
                <option value="medium">متوسطة</option>
                <option value="low">منخفضة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label">تاريخ التسليم</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
        </div>
        <button type="submit" class="btn btn-primary">حفظ المهمة</button>
        <a href="{{ route('manager.tasks.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection 