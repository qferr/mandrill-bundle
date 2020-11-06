<?php

namespace Qferrer\Symfony\MandrillBundle\Exception;

/**
 * Class AccessDeniedHttpException
 */
class AccessDeniedHttpException extends HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(403, $message, $code, $previous);
    }
}