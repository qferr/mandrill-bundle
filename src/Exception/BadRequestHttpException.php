<?php

namespace Qferrer\Symfony\MandrillBundle\Exception;

use Throwable;

/**
 * Class BadRequestHttpException
 */
class BadRequestHttpException extends HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }
}