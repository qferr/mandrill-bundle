<?php

namespace Qferrer\Tests\Symfony\MandrillBundle\Handler;

use PHPUnit\Framework\TestCase;
use Qferrer\Symfony\MandrillBundle\Event\MessageEvent;
use Qferrer\Symfony\MandrillBundle\Exception\BadRequestHttpException;
use Qferrer\Symfony\MandrillBundle\MessageEvents;
use Qferrer\Symfony\MandrillBundle\Handler\WebhookHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WebhookHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcher;

    /**
     * @var WebhookHandler
     */
    private $webhookHandler;

    public function setUp()
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->webhookHandler = new WebhookHandler($this->eventDispatcher);
    }

    public function testInvalidRequest()
    {
        $this->expectException(BadRequestHttpException::class);
        $this->webhookHandler->handleRequest(new Request());
    }

    public function testHandleRequest()
    {
        $events = json_decode(file_get_contents(__DIR__ . '/../fixtures/webhooks/all.json'), true);

        foreach ($events as $i => $event) {
            $eventName = MessageEvents::PREFIX . '.' . $event['event'];
            $this->eventDispatcher
                ->expects($this->at($i))
                ->method('dispatch')
                ->with($this->callback(function (MessageEvent $event) use($eventName) {
                    return $event->getName() === $eventName;
                }), $eventName);
        }

        $this->webhookHandler->handleRequest($this->createWebhookRequest($events));
    }

    public function testHandleMessage()
    {
        $expectedEvent = MessageEvent::create();
        $expectedEvent->setName(MessageEvents::SEND);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (MessageEvent $event) use($expectedEvent) {
                return $event->getName() === $expectedEvent->getName();
            }), $expectedEvent->getName());

        $this->webhookHandler->handleMessage($expectedEvent);
    }

    private function createWebhookRequest(array $data = [])
    {
        $request = new Request();
        $request->request->set('mandrill_events', json_encode($data));

        return $request;
    }
}