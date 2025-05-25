<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $message = 'Недостаточно средств на балансе';
}
