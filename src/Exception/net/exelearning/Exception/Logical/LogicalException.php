<?php

namespace App\Exception\net\exelearning\Exception\Logical;

/**
 * LogicalException.
 */
class LogicalException extends \Exception
{
    private $description;

    private $className;

    /**
     * @param string $description
     * @param string $className
     */
    public function __construct($description, $className = null)
    {
        parent::__construct($description);

        $this->description = $description;
        $this->className = $className;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getClassName()
    {
        return $this->className;
    }
}
