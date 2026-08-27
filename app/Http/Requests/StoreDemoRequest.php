<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDemoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'business_name' => ['required', 'string', 'max:160'],
            'work_email' => ['required', 'string', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'location' => ['nullable', 'string', 'max:160'],
            'document_types' => ['required', 'array', 'min:1', 'max:6'],
            'document_types.*' => [
                'string',
                Rule::in(['invoices', 'receipts', 'purchase-orders', 'statements', 'delivery-notes', 'other']),
            ],
            'monthly_document_volume' => [
                'nullable',
                'string',
                Rule::in(['under-100', '100-500', '501-1000', '1001-3000', '3000-plus', 'not-sure']),
            ],
            'current_process' => ['nullable', 'string', 'max:2000'],
            'biggest_challenge' => ['nullable', 'string', 'max:2000'],
            'preferred_contact_method' => ['nullable', 'string', Rule::in(['email', 'phone', 'whatsapp'])],
            'message' => ['nullable', 'string', 'max:3000'],
            'website' => ['nullable', 'prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_types.required' => 'Choose at least one type of document your team processes.',
            'document_types.min' => 'Choose at least one type of document your team processes.',
        ];
    }
}
