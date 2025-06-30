<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Team;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(array_keys(config('constants.task_statuses')))],
            'priority' => ['sometimes', 'required', Rule::in(array_keys(config('constants.task_priorities')))],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'assigned_user_id' => ['sometimes', 'required', 'exists:users,id'],
            'team_id' => ['sometimes', 'required', 'exists:teams,id'],
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
            'title.required' => config('constants.validation_messages.task.title_required'),
            'title.max' => config('constants.validation_messages.task.title_max'),
            'status.required' => config('constants.validation_messages.task.status_required'),
            'status.in' => config('constants.validation_messages.task.status_invalid'),
            'priority.required' => config('constants.validation_messages.task.priority_required'),
            'priority.in' => config('constants.validation_messages.task.priority_invalid'),
            'due_date.date' => config('constants.validation_messages.task.due_date_date'),
            'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون اليوم أو بعده',
            'assigned_user_id.required' => config('constants.validation_messages.task.assigned_user_required'),
            'assigned_user_id.exists' => config('constants.validation_messages.task.assigned_user_exists'),
            'team_id.required' => config('constants.validation_messages.task.team_required'),
            'team_id.exists' => config('constants.validation_messages.task.team_exists'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $teamId = $this->input('team_id');
            $userId = $this->input('assigned_user_id');
            
            if ($teamId && $userId) {
                $team = Team::find($teamId);
                if ($team && !$team->users()->where('user_id', $userId)->exists()) {
                    $validator->errors()->add('assigned_user_id', config('constants.validation_messages.task.user_not_in_team'));
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان المهمة',
            'description' => 'وصف المهمة',
            'status' => 'حالة المهمة',
            'priority' => 'أولوية المهمة',
            'due_date' => 'تاريخ الاستحقاق',
            'assigned_user_id' => 'العضو المكلف',
            'team_id' => 'الفريق',
        ];
    }
}
