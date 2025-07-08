@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">إضافة مهمة جديدة</h2>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" name="title" id="title" class="form-control" required value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="team_id" class="form-label">الفريق</label>
            <select name="team_id" id="team_id" class="form-select" required>
                <option value="">اختر الفريق</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="assigned_user_id" class="form-label">المكلف</label>
            <select name="assigned_user_id" id="assigned_user_id" class="form-select" required>
                <option value="">اختر العضو</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending">قيد الانتظار</option>
                <option value="in_progress">قيد التنفيذ</option>
                <option value="completed">مكتملة</option>
                <option value="canceled">ملغاة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="priority" class="form-label">الأولوية</label>
            <select name="priority" id="priority" class="form-select" required>
                <option value="high">عالية</option>
                <option value="medium">متوسطة</option>
                <option value="low">منخفضة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label">تاريخ التسليم</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
        </div>
        <button type="submit" class="btn btn-primary">حفظ المهمة</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const teamSelect = document.getElementById('team_id');
    const memberSelect = document.getElementById('assigned_user_id');
    
    // حفظ القيمة المحددة مسبقاً (إذا كانت موجودة)
    const oldTeamId = '{{ old("team_id") }}';
    const oldMemberId = '{{ old("assigned_user_id") }}';
    
    // إذا كان هناك فريق محدد مسبقاً، قم بتحميل أعضائه
    if (oldTeamId) {
        loadTeamMembers(oldTeamId, oldMemberId);
    }
    
    teamSelect.addEventListener('change', function() {
        const teamId = this.value;
        if (teamId) {
            loadTeamMembers(teamId);
        } else {
            // إفراغ قائمة الأعضاء إذا لم يتم اختيار فريق
            memberSelect.innerHTML = '<option value="">اختر العضو</option>';
            memberSelect.disabled = true;
        }
    });
    
    function loadTeamMembers(teamId, selectedMemberId = null) {
        // إظهار مؤشر التحميل
        memberSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        memberSelect.disabled = true;
        
        fetch(`/teams/${teamId}/members`)
            .then(response => response.json())
            .then(data => {
                memberSelect.innerHTML = '<option value="">اختر العضو</option>';
                
                data.members.forEach(member => {
                    const option = document.createElement('option');
                    option.value = member.id;
                    option.textContent = member.name;
                    
                    // تحديد العضو إذا كان محدداً مسبقاً
                    if (selectedMemberId && member.id == selectedMemberId) {
                        option.selected = true;
                    }
                    
                    memberSelect.appendChild(option);
                });
                
                memberSelect.disabled = false;
            })
            .catch(error => {
                console.error('خطأ في تحميل أعضاء الفريق:', error);
                memberSelect.innerHTML = '<option value="">خطأ في تحميل الأعضاء</option>';
                memberSelect.disabled = true;
            });
    }
});
</script>
@endsection 