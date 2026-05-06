<?php

namespace App\Http\Requests;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'priority' => ['sometimes', Rule::enum(IssuePriority::class)],
            'category' => ['sometimes', Rule::enum(IssueCategory::class)],
            'status' => ['sometimes', Rule::enum(IssueStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required to identify the issue.',
            'title.max' => 'The title must be 255 characters or less.',
            'description.required' => 'A description is required to understand the issue.',
            'description.max' => 'The description must be 10,000 characters or less.',
            'priority.*' => 'Priority must be one of: low, medium, high, critical.',
            'category.*' => 'Category must be one of: bug, feature_request, support, infrastructure, security, other.',
            'status.*' => 'Status must be one of: open, in_progress, resolved, closed.',
        ];
    }
}
