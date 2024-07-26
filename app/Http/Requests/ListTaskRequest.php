<?php

namespace App\Http\Requests;

use App\Enums\TaskOrderByEnum;
use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Services\TaskService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ListTaskRequest extends FormRequest
{
    /**
     * Query params that can have default values
     */
    private static function paramsWithDefaultValues(): array
    {
        return [
            'order_by' => TaskOrderByEnum::Latest->value,
            'per_page' => TaskService::DEFAULT_PER_PAGE
        ];
    }

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
            'per_page' => ['sometimes', 'integer', 'max:20'],
            'author' => ['sometimes', Rule::exists('users')],
            'status' => ['sometimes', new Enum(TaskStatusEnum::class)],
            'priority' => ['sometimes', new Enum(TaskPriorityEnum::class)],
            'order_by' => ['sometimes', new Enum(TaskOrderByEnum::class)],
            'search' => ['sometimes', 'string', 'max:100'],
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
            'order_by.Illuminate\Validation\Rules\Enum' => 'Invalid order_by criteria specified',
            'search.max' => 'Search term provided is too long'
        ];
    }

    /**
     * provide fallbacks to improve frontend UX
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        foreach (self::paramsWithDefaultValues() as $param => $value) {
            if (! isset($this->{$param})) {
                $this->merge([$param => $value]);
            }
        }
    }
}
