<?php

namespace Qferrer\Symfony\MandrillBundle\Exception;

use Throwable;

/**
 * Class HttpException
 */
class HttpException extends \RuntimeException
{
    private $statusCode;

    public function __construct(int $statusCode, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}