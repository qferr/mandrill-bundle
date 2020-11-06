Webhooks
========

This bundle provides services to handle Mandrill's Webhooks:
- a `WebhookController` controller to handle Mandrill's webhook requests;
- a `WebhookAuthentication` service to verify the request to ensure that the data is coming from Mandrill and not a third-party pretending to be;
- a `WebhookHandler` handler to let you perform a custom action when a message event occurs, by making use of the Event Dispatcher component;
- a `MessageEvents`, the list of events to listen to suit the specific needs of your application;
- a `MessageEvent`, an object that represents a message event data sent by Mandrill.

### Why using Mandrill Webhooks?

Mandrill's webhooks offer many possibilities. Here are some examples:
- **Reduce bounce rate**: listen for bounces events, then remove invalid emails from your mailing lists.
- **Avoid spam complaint rate**: listen for spam events, then delete the email that flagged your message as spam from your mailing lists.
- **Synchronize database in real-time**: listen for send events, then mark your entity as sent in your database.
- **Log events of email**: listen for one or more events, then log them to your preferred location by using a  logger.

Configuration
-------------

### Configuring Environment Variables
Add your Mandrill Webhook key and URL inside the `.env` file:

```
# .env
MANDRILL_WEBHOOK_KEY=6LfoM_YUAAAAACd3tyP82gpjQRrjJar-AoHaCyVQ
MANDRILL_WEBHOOK_URL=https://acme.com/mandrill/hooks
```

The `MANDRILL_WEBHOOK_KEY` key is used to identify the webhook. You can retrieve or reset the key 
from your Webhooks page in your account, in the Key column. 

The `MANDRILL_WEBHOOK_URL` key is used to authenticate the Mandrill's request. 
It's the URL that you used to configure the webhook in Mandrill.

### Configuring the bundle
By default, the authentication is enabled. Therefore, you need to specify the webhook key and webhook URL to verify the request signature:

```
# config/packages/mandrill.yaml
parameters:
  mandrill_webhook_key: '%env(resolve:MANDRILL_WEBHOOK_KEY)%'
  mandrill_webhook_url: '%env(resolve:MANDRILL_WEBHOOK_URL)%'

qferrer_mandrill:
  webhooks:
    key: "%mandrill_webhook_key%"
    url: "%mandrill_webhook_key%"
```

This is not recommended but if you want to disable the authentication (e.g. in dev environment), set the authentication to false:

```
# config/packages/dev/mandrill.yaml
qferrer_mandrill:
  webhooks:
    auth: false
```

### Mapping a URL to Webhook Controller
The `WebhookController` controller accepts Mandrill's webhook request and returns the response to Mandrill.

```
# config/routes.yaml
mandrill_hooks:
  path: /mandrill/hooks
  controller: Qferrer\Symfony\MandrillBundle\Controller\WebhookController::handle
  methods: POST|GET|HEAD
```

The controller accepts only `POST` requests and supports the Mandrill check they do to verify that the 
URL defined in the Mandrill app exists by using a `HEAD` request. Symfony silently transforms `HEAD` requests to `GET`. 

Read more on the [Mandrill documentation](https://mandrill.zendesk.com/hc/en-us/articles/360038739814-Introduction-to-Webhooks).

Usage
-----

### Creating a Listener
When a Mandrill's webhook is triggered, it's identified by a unique event name, which any number of listeners might be listening to. 
All events can be found in the constants of the `MessageEvents` class:
- `MessageEvents.SEND`: The message is sent
- `MessageEvents.DELAYED`: The message is delayed
- `MessageEvents.SOFT_BOUNCE`: The message is soft-bounced
- `MessageEvents.HARD_BOUNCE`: The message is hard-bounced
- `MessageEvents.SPAM`: The message is marked as spam
- `MessageEvents.REJECTED`: The message is rejected
- `MessageEvents.UNSUBSCRIBE`: The message recipient unsubscribe
- `MessageEvents.OPENED`: The message is opened
- `MessageEvents.CLICKED`: The link in the message is clicked

The following example shows an event subscriber that defines a method that listens to Mandrill's events and logs the message.

```
<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Qferrer\Symfony\MandrillBundle\Event\MessageEvent;
use Qferrer\Symfony\MandrillBundle\MessageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoggerListener
 */
class LoggerListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggerListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvents::SEND => 'log',
            MessageEvents::REJECTED => 'log',
            MessageEvents::HARD_BOUNCE => 'log',
            MessageEvents::SOFT_BOUNCE => 'log',
            MessageEvents::SPAM => 'log',
        ];
    }

    /**
     * Log the event of an email
     *
     * @param MessageEvent $event
     */
    public function log(MessageEvent $event)
    {
        $message = $event->getMessage();

        switch ($event->getName()) {
            case MessageEvents::REJECTED:
            case MessageEvents::HARD_BOUNCE:
                $level = LogLevel::ERROR;
                break;
            case MessageEvents::SOFT_BOUNCE:
            case MessageEvents::SPAM:
                $level = LogLevel::WARNING;
                break;
            default:
                $level = LogLevel::INFO;
        }

        $this->logger->log(
            $level,
            sprintf('Message "%s" has been handled.', $message['_id']),
            $message
        );
    }
}
```

Testing and Debugging
---------------------

### Using Mandrill
Mandrill has built-in testing tools to send test events to any webhook URL. To test a webhook using the Mandrill web application:
1. Navigate to **Settings** in your Mandrill account.
2. Click **Webhooks** from the top menu.
3. Click the **send test** button to send a batch of events to your webhook URL.

Your webhook URL must be public to allow Mandrill to send you test events. You should use third-party tools such as ngrok to create a secure public URL to a local server on your machine.

### Using cURL
First, you need to disable the authentication in dev environment:

```
# config/packages/dev/mandrill.yaml
qferrer_mandrill:
  webhooks:
    auth: false
```

Then, execute the following command example to send a test event to your webhook URL:

```
curl --location --request POST 'localhost/mandrill/hooks' \
--form 'mandrill_events=[{"event": "send", "msg": {"_id": "testSend", "to": "john@acme.com", "status": "sent"}, "_id": "testSend", "ts": 1384954004}]'
```