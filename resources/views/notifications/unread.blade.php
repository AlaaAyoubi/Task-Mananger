@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="h4 mb-0">
                                <i class="fas fa-eye me-2"></i>
                                {{ __('الإشعارات غير المقروءة') }}
                            </h2>
                            <div class="d-flex gap-2">
                                <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-bell me-1"></i>
                                    جميع الإشعارات
                                </a>
                                @if($notifications->count() > 0)
                                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-double me-1"></i>
                                            تحديد الكل كمقروء
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($notifications->count() > 0)
                            <div class="row">
                                @foreach($notifications as $notification)
                                    <div class="col-12 mb-3">
                                        <div class="card border-primary bg-primary bg-opacity-10">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-primary rounded-pill me-2">جديد</span>
                                                            <small class="text-muted me-2">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                            <span class="badge 
                                                                @if($notification->type === 'task_created') bg-success
                                                                @elseif($notification->type === 'task_updated') bg-warning
                                                                @elseif($notification->type === 'task_status_changed') bg-info
                                                                @else bg-secondary
                                                                @endif">
                                                                @if($notification->type === 'task_created') مهمة جديدة
                                                                @elseif($notification->type === 'task_updated') تحديث مهمة
                                                                @elseif($notification->type === 'task_status_changed') تغيير حالة
                                                                @else إشعار
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <p class="mb-2">{{ $notification->message }}</p>
                                                        
                                                        @if($notification->data && is_array($notification->data))
                                                            <div class="small text-muted">
                                                                @if(isset($notification->data['task_title']))
                                                                    <p class="mb-1"><strong>المهمة:</strong> {{ $notification->data['task_title'] }}</p>
                                                                @endif
                                                                @if(isset($notification->data['team_id']))
                                                                    @php
                                                                        $team = \App\Models\Team::find($notification->data['team_id']);
                                                                    @endphp
                                                                    <p class="mb-0"><strong>الفريق:</strong> {{ $team ? $team->name : 'غير محدد' }}</p>
                                                                @endif
                                                                @if(isset($notification->data['created_by']))
                                                                    <p class="mb-0"><strong>أنشأ بواسطة:</strong> {{ $notification->data['created_by'] }}</p>
                                                                @endif
                                                                @if(isset($notification->data['updated_by']))
                                                                    <p class="mb-0"><strong>حدث بواسطة:</strong> {{ $notification->data['updated_by'] }}</p>
                                                                @endif
                                                                @if(isset($notification->data['changed_by']))
                                                                    <p class="mb-0"><strong>غير بواسطة:</strong> {{ $notification->data['changed_by'] }}</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-check me-1"></i>
                                                                تحديد كمقروء
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash me-1"></i>
                                                                حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h4 class="text-success">لا توجد إشعارات غير مقروءة</h4>
                                <p class="text-muted">جميع إشعاراتك مقروءة</p>
                                <a href="{{ route('notifications.index') }}" class="btn btn-primary">
                                    <i class="fas fa-bell me-1"></i>
                                    عرض جميع الإشعارات
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 