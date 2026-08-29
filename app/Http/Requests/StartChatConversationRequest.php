<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartChatConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:120'],
            'visitor_email' => ['required', 'string', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:2000'],
            'website' => ['nullable', 'prohibited'],
        ];
    }
}
