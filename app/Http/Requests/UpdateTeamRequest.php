<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $teamId = $this->route('team')->id ?? $this->route('team');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('teams', 'name')->ignore($teamId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'manager_id' => ['sometimes', 'required', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => config('constants.validation_messages.team.name_required'),
            'name.max' => config('constants.validation_messages.team.name_max'),
            'name.unique' => 'اسم الفريق مستخدم بالفعل',
            'description.max' => config('constants.validation_messages.team.description_max'),
            'manager_id.required' => 'يجب تحديد مدير للفريق',
            'manager_id.exists' => 'المدير المحدد غير موجود',
            'members.array' => 'الأعضاء يجب أن تكون قائمة',
            'members.*.exists' => 'أحد الأعضاء المحددين غير موجود',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم الفريق',
            'description' => 'وصف الفريق',
            'manager_id' => 'مدير الفريق',
            'members' => 'أعضاء الفريق',
        ];
    }
} 