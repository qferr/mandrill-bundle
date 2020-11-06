<?php

namespace Qferrer\Symfony\MandrillBundle\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebhookAuthentication.
 *
 * @see https://mandrill.zendesk.com/hc/en-us/articles/205583257-How-to-Authenticate-Webhook-Requests
 */
class WebhookAuthentication implements WebhookAuthenticationInterface
{
    /**
     * @var string
     */
    private $webhookKey;

    /**
     * @var string
     */
    private $webhookUrl;

    /**
     * WebhookAuthentication constructor.
     *
     * @param string $webhookKey The webhook's authentication key
     * @param string $webhookUrl The webhook url
     */
    public function __construct(string $webhookKey, string $webhookUrl)
    {
        $this->webhookKey = $webhookKey;
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated(Request $request): bool
    {
        $requestSignature = $this->getRequestSignature($request->request->all());

        if ($requestSignature === $request->headers->get('X-Mandrill-Signature')) {
            return true;
        }

        return false;
    }

    /**
     * Generates a base64-encoded signature for a Mandrill web hook request.
     *
     * @param array $webHookRequestData The request's POST parameters
     *
     * @return string
     */
    private function getRequestSignature(array $webHookRequestData = []): string
    {
        $signedData = $this->webhookUrl;

        ksort($webHookRequestData);

        foreach ($webHookRequestData as $key => $value) {
            $signedData .= $key;
            $signedData .= $value;
        }

        return base64_encode(hash_hmac('sha1', $signedData, $this->webhookKey, true));
    }
}
