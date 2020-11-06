<?php

namespace Qferrer\Symfony\MandrillBundle\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface WebhookAuthenticationInterface
 */
interface WebhookAuthenticationInterface
{
    /**
     * Verifying the request signatures.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool;
}