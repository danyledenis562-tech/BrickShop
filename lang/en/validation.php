<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'max' => [
        'string' => 'The :attribute must not be greater than :max characters.',
        'numeric' => 'The :attribute must not be greater than :max.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'phone' => 'phone',
        'city' => 'city',
        'address' => 'address',
    ],
];
