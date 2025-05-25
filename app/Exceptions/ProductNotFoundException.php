<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    protected $message = 'Товар не найден';
}
