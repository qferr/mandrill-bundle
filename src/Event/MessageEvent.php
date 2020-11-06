<?php

namespace Qferrer\Symfony\MandrillBundle\Event;

use Qferrer\Symfony\MandrillBundle\Exception\InvalidArgumentException;
use Qferrer\Symfony\MandrillBundle\MessageEvents;

/**
 * Class MessageEvent.
 *
 * @see https://mandrill.zendesk.com/hc/en-us/articles/205583307-Message-Event-Webhook-format
 */
class MessageEvent
{
    /**
     * The event id.
     *
     * @var string
     */
    protected $id;

    /**
     * The event name.
     *
     * @var string
     */
    protected $name;

    /**
     * The message.
     *
     * @var array
     */
    protected $message;

    /**
     * The date when the event occurred.
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * Factory method.
     *
     * @param array $data
     *
     * @return static
     */
    public static function create(array $data = []): MessageEvent
    {
        $event = new static();

        if (!empty($data)) {
            $event
                ->setId($data['_id'] ?? '')
                ->setName($data['event'] ?? '')
                ->setMessage($data['msg'] ?? []);

            $date = new \DateTime();
            $date->setTimestamp($data['ts']);

            $event->setDate($date);
        }

        return $event;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return MessageEvent
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return MessageEvent
     */
    public function setName(string $name): MessageEvent
    {
        if (!in_array($name, MessageEvents::all())) {
            throw new InvalidArgumentException(sprintf(
                'The event name "%s" is not valid. ("%s")',
                $name,
                implode(", ", MessageEvents::all())
            ));
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getMessage(): array
    {
        return $this->message;
    }

    /**
     * @param array $message
     *
     * @return MessageEvent
     */
    public function setMessage(array $message = []): MessageEvent
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return MessageEvent
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }
}
