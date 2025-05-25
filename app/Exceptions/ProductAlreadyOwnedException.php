<?php

namespace App\Exceptions;

use Exception;

class ProductAlreadyOwnedException extends Exception
{
    protected $message = 'Пользователь уже владеет этим товаром';
}
