<?php

namespace App\Exceptions;

use Exception;

class ProductNotAvailableException extends Exception
{
    protected $message = 'Товар недоступен для аренды';
}
