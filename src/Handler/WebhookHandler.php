<?php

namespace Qferrer\Symfony\MandrillBundle\Handler;

use Qferrer\Symfony\MandrillBundle\Event\MessageEvent;
use Qferrer\Symfony\MandrillBundle\Exception\BadRequestHttpException;
use Qferrer\Symfony\MandrillBundle\MessageEvents;
use Qferrer\Symfony\MandrillBundle\Utils\JsonUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class WebhookHandler
 */
class WebhookHandler implements WebhookHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * WebhookHandler constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(Request $request): void
    {
        $events = $this->getMessageEvents($request);

        foreach ($events as $event) {
            $this->handleMessage($event);
        }
    }

    /**
     * @inheritDoc
     */
    public function handleMessage(MessageEvent $event): void
    {
        $this->eventDispatcher->dispatch($event, $event->getName());
    }

    /**
     * Extracts Message Events from the Request.
     *
     * @param Request $request
     *
     * @return MessageEvent[]
     *
     * @throws BadRequestHttpException When the request is invalid
     */
    protected function getMessageEvents(Request $request): array
    {
        $encodedEvents = $request->request->get('mandrill_events');

        if (!$encodedEvents) {
            throw new BadRequestHttpException('The "mandrill_events" key should not be missing.');
        }

        if (!is_string($encodedEvents)) {
            throw new BadRequestHttpException('The "mandrill_events" data should be a string.');
        }

        $decodedEvents = JsonUtils::decode($encodedEvents);

        $events = [];

        foreach ($decodedEvents as $eventData) {
            $events[] = MessageEvent::create(array_merge($eventData, [
                "event" => MessageEvents::PREFIX . '.' . $eventData['event']
            ]));
        }

        return $events;
    }
}