<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Canceled = 'canceled';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
