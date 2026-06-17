<?php

namespace App\Exceptions;

use Exception;

class InvalidAmountException extends Exception
{
    public function __construct(string $message = 'O valor informado é inválido.')
    {
        parent::__construct($message);
    }
}
