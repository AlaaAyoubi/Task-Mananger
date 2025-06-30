@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>تعديل الفريق: {{ $team->name }}</h4>
                    <a href="{{ route('manager.teams.show', $team) }}" class="btn btn-secondary">العودة</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manager.teams.update', $team) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الفريق</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $team->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">مدير الفريق</label>
                            @php
                                $manager = $team->users->first(function($user) {
                                    return $user->pivot->role === 'manager';
                                });
                            @endphp
                            <input type="text" class="form-control" value="{{ $manager ? $manager->name : 'غير محدد' }}" readonly>
                            <small class="text-muted">لا يمكن للمدير تغيير مدير الفريق</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">أعضاء الفريق</label>
                            <div class="row">
                                @foreach($users as $user)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="members[]" value="{{ $user->id }}" 
                                                   id="user_{{ $user->id }}"
                                                   {{ in_array($user->id, old('members', $team->users->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="user_{{ $user->id }}">
                                                {{ $user->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('members')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('manager.teams.show', $team) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">تحديث الفريق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 