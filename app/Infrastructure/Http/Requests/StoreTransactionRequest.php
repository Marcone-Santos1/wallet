<?php

namespace Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payee_id' => [
                'required',
                Rule::exists('users', 'id'),
            ],
            'value' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
