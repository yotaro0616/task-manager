<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            // title
            'title.required' => 'タスクのタイトルを入力してください。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',

            // category_id
            'category_id.exists' => '選択されたカテゴリーは存在しません。',

            // description
            'description.string' => '説明文は文字列で入力してください。',

            // due_date
            'due_date.date' => '期限には有効な日付を入力してください。',

            // status
            'status.required' => 'ステータスを選択してください。',
            'status.in' => '指定されたステータス以外は選択できません。',

            // tags
            'tags.array' => 'タグの形式が正しくありません。',
            'tags.*.exists' => '選択されたタグの中に存在しないものが含まれています。',
        ];
    }
}
