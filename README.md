MandrillBundle
==============

This bundle provides integration of the following Mandrill's features:
- [Webhooks](docs/webhooks.md): listen to message events (send, hard-bounce, spam, ...) 
and perform custom actions based on your specific application needs.

Installation
------------
### Downloading using composer:

`composer require qferr/mandrill-bundle`

*Note that the bundle supports Symfony 4/5 and PHP 7.2+*

### Enable the bundle
Your bundle should be automatically enabled by Flex. 
In case you don't use Flex, you'll need to manually enable the bundle by adding the following line in the config/bundles.php file of your project:

```:php
<?php

return [
    // ...
    Qferrer\Symfony\MandrillBundle\QferrerMandrillBundle::class => ['all' => true],
];
```

Configuration reference
-----------------------

All options are configured under the `qferrer_mandrill` key in your application configuration:

```
qferrer_mandrill:
  webhooks:
    key: "%mandrill_webhook_key%"
    url: "%mandrill_webhook_key%"
    handler: "Qferrer\\Symfony\\MandrillBundle\\Handler\\WebhookHandler" # default handler
    auth: "Qferrer\\Symfony\\MandrillBundle\\Security\\WebhookAuthentication" # default authentication service
```

### Webhooks
#### key
- **type**: string
- **required**: only if authenticated is enabled
#### url
- **type**: string
- **required**: only if authenticated is enabled
#### handler
- **type**: string
- **default**: `Qferrer\Symfony\MandrillBundle\Handler\WebhookHandler`
- **required**: true
#### auth
- **type**: string|bool
- **default**: `Qferrer\Symfony\MandrillBundle\Security\WebhookAuthentication`
- **required**: true