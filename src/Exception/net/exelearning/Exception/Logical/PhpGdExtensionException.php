<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * PhpGdExtensionException.
 *
 * Logical exception
 */
class PhpGdExtensionException extends LogicalException
{
    // Constants
    public const GD_EXTENSION_ERROR = 'Gd extension is not installed';

    private $imagickExtensionInstalled;

    /**
     * @param string $className
     * @param bool   $gdExtensionInstalled
     */
    public function __construct($className, $gdExtensionInstalled)
    {
        $this->gdExtensionInstalled = $gdExtensionInstalled;

        parent::__construct(self::GD_EXTENSION_ERROR, $className);
    }

    public function getGdExtensionInstalled()
    {
        return $this->gdExtensionInstalled;
    }
}
