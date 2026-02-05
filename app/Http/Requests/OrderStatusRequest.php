<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:new,paid,processing,shipped,canceled'],
        ];
    }
}
