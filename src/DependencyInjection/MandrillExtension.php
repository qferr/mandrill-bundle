<?php

namespace Qferrer\Symfony\MandrillBundle\DependencyInjection;

use Qferrer\Symfony\MandrillBundle\Controller\WebhookController;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthentication;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MandrillExtension
 */
class MandrillExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('webhooks.yaml');

        $controllerDefinition = $container->getDefinition(WebhookController::class);
        $controllerDefinition->setArgument(0, new Reference($config['webhooks']['handler']));

        if ($config['webhooks']['auth']) {
            $authDefinition = $container->getDefinition(WebhookAuthentication::class);
            $authDefinition->setArgument(0, $config['webhooks']['key']);
            $authDefinition->setArgument(1, $config['webhooks']['url']);

            $controllerDefinition->setArgument(1, new Reference($config['webhooks']['auth']));
        }
    }

    /**
     * @inheritDoc
     */
    public function getAlias()
    {
        return 'qferrer_mandrill';
    }
}