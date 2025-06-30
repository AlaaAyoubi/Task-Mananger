@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">الفرق التي أديرها</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>اسم الفريق</th>
                <th>عدد الأعضاء</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->users->count() }}</td>
                    <td>
                        <a href="{{ route('manager.teams.show', $team) }}" class="btn btn-info btn-sm">عرض</a>
                        <a href="{{ route('manager.teams.edit', $team) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form action="{{ route('manager.teams.destroy', $team) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف الفريق؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">لا توجد فرق تديرها.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $teams->links() }}
</div>
@endsection 