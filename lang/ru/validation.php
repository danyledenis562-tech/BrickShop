<?php

return [
    'required' => 'Поле :attribute обязательно.',
    'email' => 'Поле :attribute должно быть корректным email.',
    'confirmed' => 'Подтверждение :attribute не совпадает.',
    'min' => [
        'string' => 'Поле :attribute должно содержать минимум :min символов.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
    ],
    'max' => [
        'string' => 'Поле :attribute не может превышать :max символов.',
        'numeric' => 'Поле :attribute не может быть больше :max.',
    ],
    'unique' => 'Поле :attribute уже занято.',
    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'password' => 'пароль',
        'phone' => 'телефон',
        'city' => 'город',
        'address' => 'адрес',
    ],
];
