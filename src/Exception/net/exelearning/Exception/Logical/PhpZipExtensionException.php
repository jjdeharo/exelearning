<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * PhpZipExtensionException.
 *
 * Logical exception
 */
class PhpZipExtensionException extends LogicalException
{
    // Constants
    public const ZIP_EXTENSION_ERROR = 'Zip extension is not installed';

    private $zipExtensionInstalled;

    /**
     * @param string $className
     * @param bool   $zipExtensionInstalled
     */
    public function __construct($className, $zipExtensionInstalled)
    {
        $this->zipExtensionInstalled = $zipExtensionInstalled;

        parent::__construct(self::ZIP_EXTENSION_ERROR, $className);
    }

    public function getZipExtensionInstalled()
    {
        return $this->zipExtensionInstalled;
    }
}
