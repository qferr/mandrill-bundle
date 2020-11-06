<?php

namespace Qferrer\Symfony\MandrillBundle\Handler;

use Qferrer\Symfony\MandrillBundle\Event\MessageEvent;
use Qferrer\Symfony\MandrillBundle\Exception\AccessDeniedHttpException;
use Qferrer\Symfony\MandrillBundle\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface WebhookHandlerInterface
 */
interface WebhookHandlerInterface
{
    /**
     * Handles a request.
     *
     * @param Request $request
     *
     * @throws BadRequestHttpException When the request is invalid
     * @throws AccessDeniedHttpException When the request signature is invalid
     */
    public function handleRequest(Request $request): void;

    /**
     * Handles a MessageEvent.
     *
     * @param MessageEvent $event
     */
    public function handleMessage(MessageEvent $event): void;
}