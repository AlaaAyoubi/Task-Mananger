@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="mb-4">مرحبًا بك في لوحة تحكم الأدمن</h1>
            <div class="mb-3 d-flex gap-2">
                <a href="{{ route('tasks.index') }}" class="btn btn-primary">إدارة المهام</a>
                <a href="{{ route('teams.index') }}" class="btn btn-info">إدارة الفرق</a>
                <a href="{{ route('teams.create') }}" class="btn btn-success">إنشاء فريق جديد</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</div>
@endsection
