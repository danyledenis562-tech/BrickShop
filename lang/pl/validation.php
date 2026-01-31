<?php

return [
    'required' => 'Pole :attribute jest wymagane.',
    'email' => 'Pole :attribute musi być poprawnym adresem email.',
    'confirmed' => 'Potwierdzenie :attribute nie pasuje.',
    'min' => [
        'string' => 'Pole :attribute musi mieć co najmniej :min znaków.',
        'numeric' => 'Pole :attribute musi być nie mniejsze niż :min.',
    ],
    'max' => [
        'string' => 'Pole :attribute nie może przekraczać :max znaków.',
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
    ],
    'unique' => 'Pole :attribute zostało już użyte.',
    'attributes' => [
        'name' => 'imię',
        'email' => 'email',
        'password' => 'hasło',
        'phone' => 'telefon',
        'city' => 'miasto',
        'address' => 'adres',
    ],
];
