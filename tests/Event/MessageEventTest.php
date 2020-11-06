<?php

namespace Qferrer\Tests\Symfony\MandrillBundle\Event;

use PHPUnit\Framework\TestCase;
use Qferrer\Symfony\MandrillBundle\Event\MessageEvent;
use Qferrer\Symfony\MandrillBundle\Exception\InvalidArgumentException;
use Qferrer\Symfony\MandrillBundle\MessageEvents;

class MessageEventTest extends TestCase
{
    public function testCreate()
    {
        $data = [
            '_id' => 'test',
            'event' => MessageEvents::PREFIX . '.' . 'send',
            'msg' => [],
            'ts' => time()
        ];

        $event = MessageEvent::create($data);

        $this->assertEquals($data['event'], $event->getName());
        $this->assertEquals($data['_id'], $event->getId());
        $this->assertEquals($data['msg'], $event->getMessage());
        $this->assertEquals($data['ts'], $event->getDate()->getTimestamp());
    }

    public function testInvalidEventName()
    {
        $this->expectException(InvalidArgumentException::class);
        MessageEvent::create(['name' => 'invalid']);
    }
}