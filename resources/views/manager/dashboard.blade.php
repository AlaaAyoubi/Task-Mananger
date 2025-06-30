@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        لوحة تحكم المدير
                    </h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>مرحباً {{ auth()->user()->name }}!</strong>
                        يمكنك إدارة مهامك كعضو والفرق التي تديرها
                    </div>
                    
                    <div class="row g-4">
                        <!-- مهامي الموكلة لي كعضو -->
                        <div class="col-md-6">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-tasks fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">مهامي كعضو</h5>
                                    <p class="card-text text-muted">عرض وتحديث المهام الموكلة إليك كعضو في الفرق</p>
                                    <a href="{{ route('manager.my-tasks') }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>
                                        عرض مهامي
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- إدارة الفرق التي أديرها -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-users-cog fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">إدارة الفرق</h5>
                                    <p class="card-text text-muted">إدارة الفرق التي تديرها - إضافة أعضاء ومهام</p>
                                    <a href="{{ route('manager.teams') }}" class="btn btn-success">
                                        <i class="fas fa-cog me-1"></i>
                                        إدارة الفرق
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- إدارة مهام الفرق -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-clipboard-list fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">إدارة مهام الفرق</h5>
                                    <p class="card-text text-muted">إنشاء وتعديل مهام الفرق التي تديرها</p>
                                    <a href="{{ route('manager.tasks.index') }}" class="btn btn-warning">
                                        <i class="fas fa-plus me-1"></i>
                                        إدارة المهام
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- الإشعارات -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-bell fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">الإشعارات</h5>
                                    <p class="card-text text-muted">عرض الإشعارات والتحديثات الجديدة</p>
                                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-bell me-1"></i>
                                        الإشعارات
                                        @if(\App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() > 0)
                                            <span class="badge bg-danger ms-1">{{ \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
