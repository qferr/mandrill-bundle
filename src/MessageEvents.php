<?php

namespace Qferrer\Symfony\MandrillBundle;

/**
 * Class MessageEvents.
 */
final class MessageEvents
{
    /**
     * The prefix for all events.
     *
     * @var string
     */
    const PREFIX = 'mandrill.message';
    
    /**
     * Message is sent.
     *
     * @var string
     */
    const SEND = self::PREFIX . '.send';

    /**
     * Message is delayed.
     *
     * @var string
     */
    const DELAYED = self::PREFIX . '.deferral';

    /**
     * Message is Soft-bounced.
     *
     * @var string
     */
    const SOFT_BOUNCE = self::PREFIX . '.soft_bounce';

    /**
     * Message is Hard-bounced.
     *
     * @var string
     */
    const HARD_BOUNCE = self::PREFIX . '.hard_bounce';

    /**
     * Message is marked as Spam.
     *
     * @var string
     */
    const SPAM = self::PREFIX . '.spam';

    /**
     * Message is rejected.
     *
     * @var string
     */
    const REJECTED = self::PREFIX . '.reject';

    /**
     * Message recipient unsubscribe.
     *
     * @var string
     */
    const UNSUBSCRIBE = self::PREFIX . '.unsub';

    /**
     * Message is opened.
     *
     * @var string
     */
    const OPENED = self::PREFIX . '.open';

    /**
     * Message is clicked.
     *
     * @var string
     */
    const CLICKED = self::PREFIX . '.click';

    /**
     * Gets all events
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::SEND,
            self::DELAYED,
            self::SOFT_BOUNCE,
            self::HARD_BOUNCE,
            self::SPAM,
            self::REJECTED,
            self::UNSUBSCRIBE,
            self::OPENED,
            self::CLICKED
        ];
    }
}
