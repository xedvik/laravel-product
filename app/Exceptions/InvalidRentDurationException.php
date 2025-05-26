<?php

namespace App\Exceptions;

use Exception;

class InvalidRentDurationException extends Exception
{
    protected $message = 'Неверная продолжительность аренды';
}
