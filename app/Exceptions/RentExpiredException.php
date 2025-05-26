<?php

namespace App\Exceptions;

use Exception;

class RentExpiredException extends Exception
{
    protected $message = 'Аренда истекла';
}
