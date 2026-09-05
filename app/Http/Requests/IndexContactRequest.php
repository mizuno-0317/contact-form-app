<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexContactRequest extends FormRequest
{
    /**
     * リクエスの認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール（問い合わせ検索）
     */
    public function rules(): array
    {
        return [
            'keyword'=>'nullable|string|max:255',
            'gender'=>'nullable|integer|in:0,1,2,3',
            'category_id'=>'nullable|integer|exists:categories,id',
            'date'=>'nullable|date',
        ];
    }
}
