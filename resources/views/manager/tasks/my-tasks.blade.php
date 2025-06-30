<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                {{ __('مهامي كعضو') }}
            </h2>
            <div>
                <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-1"></i>
                    العودة للوحة التحكم
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- فلترة المهام -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('manager.my-tasks') }}" class="row g-3">
                                <div class="col-md-4">
                                    <select name="status" class="form-select">
                                        <option value="">جميع الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>ملغية</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="priority" class="form-select">
                                        <option value="">جميع الأولويات</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>
                                        فلترة
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>العنوان</th>
                                        <th>الفريق</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $task->team->name }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'in_progress' => 'primary',
                                                        'completed' => 'success',
                                                        'canceled' => 'danger'
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'قيد الانتظار',
                                                        'in_progress' => 'قيد التنفيذ',
                                                        'completed' => 'مكتملة',
                                                        'canceled' => 'ملغية'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                    {{ $statusLabels[$task->status] ?? $task->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'low' => 'success',
                                                        'medium' => 'warning',
                                                        'high' => 'danger'
                                                    ];
                                                    $priorityLabels = [
                                                        'low' => 'منخفضة',
                                                        'medium' => 'متوسطة',
                                                        'high' => 'عالية'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() ? 'text-danger' : '' }}">
                                                        {{ $task->due_date->format('Y-m-d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- تحديث الحالة -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-edit me-1"></i>
                                                        تحديث الحالة
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" class="dropdown-item {{ $task->status == 'pending' ? 'active' : '' }}">
                                                                    قيد الانتظار
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="in_progress">
                                                                <button type="submit" class="dropdown-item {{ $task->status == 'in_progress' ? 'active' : '' }}">
                                                                    قيد التنفيذ
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="dropdown-item {{ $task->status == 'completed' ? 'active' : '' }}">
                                                                    مكتملة
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('manager.my-tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="canceled">
                                                                <button type="submit" class="dropdown-item {{ $task->status == 'canceled' ? 'active' : '' }}">
                                                                    ملغية
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $tasks->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">لا توجد مهام مخصصة لك</h4>
                            <p class="text-muted">ستظهر هنا المهام التي يتم تعيينها لك من قبل المديرين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 