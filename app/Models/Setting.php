<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_support',
        'telegram_support_url',
        'show_support_widget',
    ];

    protected $casts = [
        'show_support_widget' => 'boolean',
    ];
}
