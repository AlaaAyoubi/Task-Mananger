@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">مهامي</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" class="mb-3 row g-2">
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
                <th>تعديل الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->team->name ?? '-' }}</td>
                    <td>
                        <span class="task-status-badge badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'warning' : ($task->status == 'canceled' ? 'danger' : 'secondary')) }}">
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
                        <form class="update-status-form d-inline" data-task-id="{{ $task->id }}" action="{{ route('tasks.updateStatus', $task) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm d-inline w-auto">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="canceled" {{ $task->status == 'canceled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success">تحديث</button>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const getStatusBadgeClass = (status) => {
        switch(status) {
            case 'completed': return 'bg-success';
            case 'in_progress': return 'bg-warning';
            case 'canceled': return 'bg-danger';
            default: return 'bg-secondary';
        }
    };

    const getStatusLabel = (status) => {
        switch(status) {
            case 'completed': return 'مكتملة';
            case 'in_progress': return 'قيد التنفيذ';
            case 'canceled': return 'ملغاة';
            default: return 'قيد الانتظار';
        }
    };

    document.querySelectorAll('.update-status-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const status = formData.get('status');

            fetch(this.getAttribute('action'), {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    _token: formData.get('_token')
                })
            })
            .then(response => response.json())
            .then(data => {
                // تحديث شارة الحالة
                const statusBadge = this.closest('tr').querySelector('.task-status-badge');
                statusBadge.className = `task-status-badge badge ${getStatusBadgeClass(data.status)}`;
                statusBadge.textContent = getStatusLabel(data.status);

                // إظهار رسالة نجاح
                const alert = document.createElement('div');
                alert.className = 'alert alert-success mt-2';
                alert.textContent = data.message;
                this.appendChild(alert);

                // إخفاء رسالة النجاح بعد 3 ثواني
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger mt-2';
                alert.textContent = 'حدث خطأ أثناء تحديث الحالة';
                this.appendChild(alert);
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            });
        });
    });
});
</script>
@endpush
@endsection 