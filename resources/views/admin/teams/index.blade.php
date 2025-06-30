@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">قائمة الفرق</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('teams.create') }}" class="btn btn-success mb-3">إنشاء فريق جديد</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>اسم الفريق</th>
                <th>المدير</th>
                <th>عدد الأعضاء</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $team)
                @php
                    $manager = $team->users->first(function($user) {
                        return $user->pivot->role === 'manager';
                    });
                @endphp
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $manager ? $manager->name : '-' }}</td>
                    <td>{{ $team->users->count() }}</td>
                    <td>
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-info btn-sm">عرض</a>
                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form action="{{ route('teams.destroy', $team) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف الفريق؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد فرق.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $teams->links() }}
</div>
@endsection 