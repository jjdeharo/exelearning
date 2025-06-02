<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * UserInsufficientSpaceException.
 */
class UserInsufficientSpaceException extends LogicalException
{
    // Constants
    public const USER_ISSUFFICIENT_SPACE_ERROR = 'The user does not have enough space';

    public function __construct()
    {
        parent::__construct(self::USER_ISSUFFICIENT_SPACE_ERROR);
    }
}
