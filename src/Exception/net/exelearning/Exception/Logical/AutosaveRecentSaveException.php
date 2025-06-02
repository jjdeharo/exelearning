<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * AutosaveRecentSaveException.
 */
class AutosaveRecentSaveException extends LogicalException
{
    // Constants
    public const RECENT_AUTOSAVE_ERROR = "There is a recent save, it's not necessary to autosave";

    public function __construct()
    {
        parent::__construct(self::RECENT_AUTOSAVE_ERROR);
    }
}
