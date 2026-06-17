<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct(string $message = 'Saldo insuficiente para realizar esta operação.')
    {
        parent::__construct($message);
    }
}
