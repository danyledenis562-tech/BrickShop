<?php

namespace App\Exceptions;

use RuntimeException;

final class OutOfStockException extends RuntimeException
{
    public function __construct(public readonly int $productId, public readonly int $requestedQty)
    {
        parent::__construct('Out of stock');
    }
}
