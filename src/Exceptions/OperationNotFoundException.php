<?php

namespace Sata\DbTest\Exceptions;

class OperationNotFoundException extends \Exception
{
    /**
     * @var string
     */
    public $operation;

    /**
     * {@inheritdoc}
     * @param string $operation
     */
    public function __construct($operation, $message = '', $code = 0, Exception $previous = null)
    {
        $this->operation = $operation;
        parent::__construct($message, $code, $previous);
    }
}