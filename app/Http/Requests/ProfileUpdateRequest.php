<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => __('messages.avatar_invalid_type'),
            'avatar.mimes' => __('messages.avatar_invalid_type'),
            'avatar.max' => __('messages.avatar_too_large'),
        ];
    }
}
