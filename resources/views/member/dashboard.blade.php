@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="mb-4">مرحبًا بك في لوحة تحكم العضو</h1>
            <div class="mb-3">
                <a href="{{ route('tasks.my') }}" class="btn btn-primary">عرض مهامي</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</div>
@endsection
