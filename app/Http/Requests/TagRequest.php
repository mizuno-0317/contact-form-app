<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    /**
     * 認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */

    public function rules(): array
    {
        return [
            'name'=>[
                'required',
                'string',
                'max:50',
                Rule::unique('tags','name')->ignore($this->tags),
            ]
        ];
    }

    /**
     * バリデーションメッセージ
     */

    public function messages():array
    {
        return[
            'name.required' =>'タグ名を入力してください',
            'name.max' => 'タグ名は文字以内で入力してください。',
            'name.unique' => 'そのタグ名は既に使用されています',
        ];
    }
}
