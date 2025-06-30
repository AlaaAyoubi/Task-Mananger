@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">تفاصيل الفريق: {{ $team->name }}</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-3">
        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning">تعديل الفريق</a>
        <form action="{{ route('teams.destroy', $team) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف الفريق؟')">حذف الفريق</button>
        </form>
        <a href="{{ route('teams.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">اسم الفريق: {{ $team->name }}</h5>
            
            @php
                $manager = $team->users->first(function($user) {
                    return $user->pivot->role === 'manager';
                });
                $members = $team->users->filter(function($user) {
                    return $user->pivot->role === 'member';
                });
            @endphp
            
            <p class="card-text"><strong>مدير الفريق:</strong> {{ $manager ? $manager->name : '-' }}</p>
            <p class="card-text"><strong>أعضاء الفريق:</strong></p>
            <ul>
                @forelse($members as $member)
                    <li>{{ $member->name }}</li>
                @empty
                    <li>لا يوجد أعضاء</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection 