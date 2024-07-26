<?php

namespace App\Http\Requests;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'title' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', new Enum(TaskStatusEnum::class)],
            'priority' => ['sometimes', new Enum(TaskPriorityEnum::class)],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'status.Illuminate\Validation\Rules\Enum' => 'Invalid task status specified',
            'priority.Illuminate\Validation\Rules\Enum' => 'Invalid task priority specified',
            'title.max' => 'Title provided is too long',
            'description.max' => 'Description provided is too long'
        ];
    }
}
