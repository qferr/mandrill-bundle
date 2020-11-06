<?php

namespace Qferrer\Tests\Symfony\MandrillBundle\Controller;

use PHPUnit\Framework\TestCase;
use Qferrer\Symfony\MandrillBundle\Controller\WebhookController;
use Qferrer\Symfony\MandrillBundle\Exception\AccessDeniedHttpException;
use Qferrer\Symfony\MandrillBundle\Handler\WebhookHandlerInterface;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthenticationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WebHookControllerTest extends TestCase
{
    /**
     * @var WebhookController
     */
    protected $webhookController;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcherInterface
     */
    private $webhookHandler;

    public function setUp()
    {
        $this->webhookHandler = $this->createMock(WebhookHandlerInterface::class);
        $this->webhookController = new WebhookController($this->webhookHandler);
    }

    public function testIsController()
    {
        $this->assertInstanceOf(AbstractController::class, $this->webhookController);
    }

    public function testMandrillCheckUrlExist()
    {
        $request = new Request();
        $this->webhookHandler->expects($this->never())->method('handleRequest');
        foreach (['GET', 'HEAD'] as $requestMethod) {
            $request->setMethod($requestMethod);
            $this->webhookHandler->expects($this->never())->method('handleRequest');
            $response = $this->webhookController->handle($request);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testOnlySupportPostMethod()
    {
        $request = new Request();
        $this->webhookHandler->expects($this->never())->method('handleRequest');
        foreach (['DELETE', 'PUT', 'PATCH'] as $requestMethod) {
            $request->setMethod($requestMethod);
            $response = $this->webhookController->handle($request);
            $this->assertEquals('Bad Request', $response->getContent());
            $this->assertEquals(400, $response->getStatusCode());
        }
    }

    public function testHandleRequest()
    {
        $request = new Request();
        $request->setMethod('POST');

        $this->webhookHandler->expects($this->once())->method('handleRequest')->with($request);
        $this->webhookController->handle($request);
    }

    public function testRequestIsAuthenticated()
    {
        $request = new Request();
        $request->setMethod('POST');

        $webhookAuthentication = $this->createMock(WebhookAuthenticationInterface::class);
        $webhookController = new WebhookController($this->webhookHandler, $webhookAuthentication);

        $webhookAuthentication->expects($this->once())->method('isAuthenticated')->willReturn(true);
        $this->webhookHandler->expects($this->once())->method('handleRequest')->with($request);
        $webhookController->handle($request);
    }

    public function testRequestIsAnonymous()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $request = new Request();
        $request->setMethod('POST');

        $webhookAuthentication = $this->createMock(WebhookAuthenticationInterface::class);
        $webhookController = new WebhookController($this->webhookHandler, $webhookAuthentication);

        $webhookAuthentication->expects($this->once())->method('isAuthenticated')->willReturn(false);
        $this->webhookHandler->expects($this->never())->method('handleRequest')->with($request);
        $webhookController->handle($request);
    }
}