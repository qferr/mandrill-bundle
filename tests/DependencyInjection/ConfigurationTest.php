<?php

namespace Qferrer\Tests\Symfony\MandrillBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qferrer\Symfony\MandrillBundle\DependencyInjection\Configuration;
use Qferrer\Symfony\MandrillBundle\Handler\WebhookHandler;
use Qferrer\Symfony\MandrillBundle\Security\WebhookAuthentication;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testEmptyConfig()
    {
        $defaultConfig = [
            "webhooks" => [
                "url" => "localhost",
                "key" => 'a1b2c3',
                "handler" => WebhookHandler::class,
                "auth" => WebhookAuthentication::class
            ]
        ];

        $config = $this->process([
            'qferrer_mandrill' => [
                "webhooks" => ["url" => "localhost", "key" => 'a1b2c3']
            ]
        ]);

        $this->assertEquals($defaultConfig, $config);
    }

    public function testDefaultConfig()
    {
        $config = $this->process([
            'qferrer_mandrill' => [
                'webhooks' => [
                    'key' => '1234',
                    'url' => 'http://localhost/mandrill/webhooks'
                ]
            ]
        ]);

        $this->assertArrayHasKey('webhooks', $config);
        $this->assertArrayHasKey('handler', $config['webhooks']);
        $this->assertArrayHasKey('auth', $config['webhooks']);
        $this->assertEquals(WebhookHandler::class, $config['webhooks']['handler']);
        $this->assertEquals(WebhookAuthentication::class, $config['webhooks']['auth']);
    }

    public function testDisableAuth()
    {
        $config = $this->process([
            'qferrer_mandrill' => [
                'webhooks' => [
                    'auth' => false
                ]
            ]
        ]);

        $this->assertFalse($config['webhooks']['auth']);
    }

    public function testWebhookUrlAndWebhookKeyAreRequired()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "qferrer_mandrill": The webhooks.key and webhooks.url should be configured to authenticate request');

        $config = $this->process([
            'qferrer_mandrill' => [
                'webhooks' => []
            ]
        ]);
    }

    protected function process($configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}