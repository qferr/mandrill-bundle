<?php

namespace Qferrer\Symfony\MandrillBundle\Controller;

use Qferrer\Symfony\MandrillBundle\Exception\AccessDeniedHttpException;
use Qferrer\Symfony\MandrillBundle\Handler\WebhookHandlerInterface;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthenticationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WebhookController.
 */
class WebhookController
{
    /**
     * @var WebhookHandlerInterface
     */
    protected $webhookHandler;

    /**
     * @var WebhookAuthenticationInterface|null
     */
    protected $webhookAuthentication;

    /**
     * WebhookController constructor.
     *
     * @param WebhookHandlerInterface $webhookHandler
     * @param WebhookAuthenticationInterface|null $webhookAuthentication
     */
    public function __construct(
        WebhookHandlerInterface $webhookHandler,
        WebhookAuthenticationInterface $webhookAuthentication = null
    ) {
        $this->webhookHandler = $webhookHandler;
        $this->webhookAuthentication = $webhookAuthentication;
    }

    /**
     * Handles the Mandrill's hook.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response
    {
        // Mandrill will do a quick check that the URL exists by using a HEAD request (not POST).
        // Note that Symfony silently transforms HEAD requests to GET.
        if (in_array($request->getMethod(), ['GET', 'HEAD'])) {
            return new Response();
        }

        // Webhook and inbound route URLs should be set up to accept, at a minimum, POST requests
        if (!$request->isMethod('POST')) {
            return new Response('Bad Request', 400);
        }

        if (!$this->isAuthenticated($request)) {
            throw new AccessDeniedHttpException();
        }

        $this->webhookHandler->handleRequest($request);

        return new Response();
    }

    /**
     * Verifying the request signatures.
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isAuthenticated(Request $request): bool
    {
        if (!$this->webhookAuthentication) {
            return true;
        }

        return $this->webhookAuthentication->isAuthenticated($request);
    }
}
