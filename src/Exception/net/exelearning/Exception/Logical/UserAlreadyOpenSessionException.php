<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * UserAlreadyOpenSessionException.
 */
class UserAlreadyOpenSessionException extends LogicalException
{
    // Constants
    public const USER_ALREADY_OPEN_SESSION_ERROR = 'user already has an open session';

    public function __construct()
    {
        parent::__construct(self::USER_ALREADY_OPEN_SESSION_ERROR);
    }
}
