<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Integration\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SentryMonologAdapter\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testProcessConfigurationWithDefaultConfiguration(): void
    {
        $expectedBundleDefaultConfig = [
            'monolog_handler_decorator' => [
                'processors' => [],
                'enabled' => false
            ],
            'messenger_logging_middleware' => [
                'enabled' => false
            ]
        ];

        $this->assertSame($expectedBundleDefaultConfig, $this->processConfiguration([]));
    }

    /**
     * @param array $monologHandlerDecoratorConfiguration
     * @param array $expectedMonologHandlerDecoratorConfiguration
     *
     * @dataProvider getMonologHandlerDecoratorOptionsProvider
     */
    public function testMonologHandlerDecoratorOptions(
        array $monologHandlerDecoratorConfiguration,
        array $expectedMonologHandlerDecoratorConfiguration
    ): void {
        $config = $this->processConfiguration(['monolog_handler_decorator' => $monologHandlerDecoratorConfiguration]);

        $this->assertSame($expectedMonologHandlerDecoratorConfiguration, $config['monolog_handler_decorator']);
    }

    public function getMonologHandlerDecoratorOptionsProvider(): array
    {
        return [
            [
                [
                    'processors' => [
                        'test_processor'
                    ]
                ],
                [
                    'processors' => [
                        'test_processor'
                    ],
                    'enabled' => true
                ]
            ]
        ];
    }

    /**
     * @param array $messengerLoggingMiddlewareConfiguration
     * @param array $expectedMessengerLoggingMiddlewareConfiguration
     *
     * @dataProvider getMessengerLoggingMiddlewareOptionsProvider
     */
    public function testMessengerLoggingMiddlewareOptions(
        array $messengerLoggingMiddlewareConfiguration,
        array $expectedMessengerLoggingMiddlewareConfiguration
    ): void {
        $config = $this->processConfiguration([
            'messenger_logging_middleware' => $messengerLoggingMiddlewareConfiguration
        ]);

        $this->assertSame($expectedMessengerLoggingMiddlewareConfiguration, $config['messenger_logging_middleware']);
    }

    public function getMessengerLoggingMiddlewareOptionsProvider(): array
    {
        return [
            [
                [
                    'logging_strategy' => [
                        'id' => 'test_logging_strategy'
                    ]
                ],
                [
                    'logging_strategy' => [
                        'id' => 'test_logging_strategy',
                        'options' => []
                    ],
                    'enabled' => true
                ]
            ]
        ];
    }

    private function processConfiguration(array $values): array
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), ['sentry_monolog_adapter' => $values]);
    }
}
