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
    'password' => [
        'letters' => 'Поле :attribute має містити щонайменше одну літеру.',
        'mixed' => 'Поле :attribute має містити щонайменше одну велику та одну малу літеру.',
        'numbers' => 'Поле :attribute має містити щонайменше одну цифру.',
        'symbols' => 'Поле :attribute має містити щонайменше один символ (наприклад, !, @, #).',
        'uncompromised' => 'Цей :attribute зʼявився у витоку даних. Оберіть інший пароль.',
    ],
    'attributes' => [
        'name' => "ім'я",
        'email' => 'email',
        'password' => 'пароль',
        'password_confirmation' => 'підтвердження пароля',
        'phone' => 'телефон',
        'city' => 'місто',
        'address' => 'адреса',
        'full_name' => 'ПІБ',
        'delivery_type' => 'спосіб доставки',
        'payment_type' => 'спосіб оплати',
        'note' => 'примітка',
        'quantity' => 'кількість',
    ],
];
