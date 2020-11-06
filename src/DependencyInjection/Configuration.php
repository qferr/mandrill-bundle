<?php

namespace Qferrer\Symfony\MandrillBundle\DependencyInjection;

use Qferrer\Symfony\MandrillBundle\Handler\WebhookHandler;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthentication;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('qferrer_mandrill');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('webhooks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('key')->cannotBeEmpty()->end()
                        ->scalarNode('url')->cannotBeEmpty()->end()
                        ->scalarNode('handler')->defaultValue(WebhookHandler::class)->end()
                        ->scalarNode('auth')->defaultValue(WebhookAuthentication::class)->end()
                    ->end()
                ->end()
            ->end();

        $rootNode
            ->validate()
                ->ifTrue(function ($v) {
                    $webhooks = $v['webhooks'];
                    return $webhooks['auth'] && (!isset($webhooks['key']) || !isset($webhooks['url']));
                })
                ->thenInvalid('The webhooks.key and webhooks.url should be configured to authenticate request')
            ->end();

        return $treeBuilder;
    }
}