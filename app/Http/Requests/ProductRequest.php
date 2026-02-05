<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'age' => ['nullable', 'integer', 'min:1', 'max:99'],
            'difficulty' => ['nullable', 'string', 'max:50'],
            'pieces' => ['nullable', 'integer', 'min:1'],
            'brand' => ['nullable', 'string', 'max:50'],
            'series' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
