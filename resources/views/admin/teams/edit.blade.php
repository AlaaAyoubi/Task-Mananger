@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">تعديل الفريق: {{ $team->name }}</h2>
    <form action="{{ route('teams.update', $team) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">اسم الفريق</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $team->name) }}">
        </div>
        <div class="mb-3">
            <label for="manager_id" class="form-label">مدير الفريق</label>
            <select name="manager_id" id="manager_id" class="form-select" required>
                <option value="">اختر المدير</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (old('manager_id', optional($team->users->firstWhere('role', 'manager'))->id)) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="members" class="form-label">أعضاء الفريق (اختياري)</label>
            <select name="members[]" id="members" class="form-select" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (collect(old('members', $team->users->where('role', 'member')->pluck('id')->toArray()))->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">يمكنك اختيار أكثر من عضو بالضغط على Ctrl أثناء التحديد.</small>
        </div>
        <button type="submit" class="btn btn-success">تحديث الفريق</button>
        <a href="{{ route('teams.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection 