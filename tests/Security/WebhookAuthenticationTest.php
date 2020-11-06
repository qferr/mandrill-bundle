<?php

namespace Qferrer\Tests\Symfony\MandrillBundle\Security;

use PHPUnit\Framework\TestCase;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthentication;
use Symfony\Component\HttpFoundation\Request;

class WebhookAuthenticationTest extends TestCase
{
    private $webhookKey;
    private $webhookUrl;
    private $webhookAuthentication;

    public function setUp()
    {
        $this->webhookKey = '1234';
        $this->webhookUrl = 'http://localhost';
        $this->webhookAuthentication = new WebhookAuthentication($this->webhookKey, $this->webhookUrl);
    }

    public function testIsAuthenticated()
    {
        $request = $this->createWebhookRequest();
        $requestSignature = $this->getSignature($request);

        $request->headers->set('X-Mandrill-Signature', $requestSignature);

        $this->assertTrue($this->webhookAuthentication->isAuthenticated($request));
    }

    public function testIsNotAuthenticated()
    {
        $request = $this->createWebhookRequest();

        $request->headers->set('X-Mandrill-Signature', 'xxxxx');

        $this->assertFalse($this->webhookAuthentication->isAuthenticated($request));
    }

    private function getSignature(Request $request)
    {
        $webHookRequestData = $request->request->all();

        $signedData = $this->webhookUrl;

        ksort($webHookRequestData);

        foreach ($webHookRequestData as $key => $value) {
            $signedData .= $key;
            $signedData .= $value;
        }

        return base64_encode(hash_hmac('sha1', $signedData, $this->webhookKey, true));
    }

    private function createWebhookRequest(array $data = [])
    {
        $request = new Request();
        $request->request->set('mandrill_events', json_encode($data));

        return $request;
    }
}