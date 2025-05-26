<?php

namespace App\Exceptions;

use Exception;

class OwnershipNotFoundException extends Exception
{
    protected $message = 'Аренда не найдена';
}