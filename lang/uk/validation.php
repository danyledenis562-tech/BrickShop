<?php

return [
    'required' => 'Поле :attribute є обовʼязковим.',
    'email' => 'Поле :attribute повинно бути дійсною email адресою.',
    'confirmed' => 'Підтвердження поля :attribute не співпадає.',
    'min' => [
        'string' => 'Поле :attribute має містити щонайменше :min символів.',
        'numeric' => 'Поле :attribute має бути не менше :min.',
    ],
    'max' => [
        'string' => 'Поле :attribute не може перевищувати :max символів.',
        'numeric' => 'Поле :attribute не може бути більше :max.',
    ],
    'unique' => 'Значення поля :attribute вже зайняте.',
    'attributes' => [
        'name' => "ім'я",
        'email' => 'email',
        'password' => 'пароль',
        'phone' => 'телефон',
        'city' => 'місто',
        'address' => 'адреса',
    ],
];
