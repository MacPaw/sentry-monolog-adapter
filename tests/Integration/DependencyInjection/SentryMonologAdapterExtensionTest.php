<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Integration\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sentry\Monolog\Handler;
use SentryMonologAdapter\DependencyInjection\SentryMonologAdapterExtension;
use SentryMonologAdapter\Messenger\LoggingStrategy\LogAfterPositionStrategy;
use SentryMonologAdapter\Messenger\Middleware\MessengerLoggingMiddleware;
use SentryMonologAdapter\Monolog\Handler\MonologHandlerDecorator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Throwable;

class SentryMonologAdapterExtensionTest extends TestCase
{
    public function testWithEmptyConfig(): void
    {
        $container = $this->createContainerFromFixture('empty_bundle_config');

        //phpcs:disable Generic.Files.LineLength
        try {
            $monologHandlerDecoratorDefinition = $container->getDefinition(MonologHandlerDecorator::class);
        } catch (Throwable $exception) {
            self::assertInstanceOf(ServiceNotFoundException::class, $exception);
            self::assertSame(
                'You have requested a non-existent service "SentryMonologAdapter\Monolog\Handler\MonologHandlerDecorator".',
                $exception->getMessage()
            );
        }

        try {
            $messengerLoggingMiddleware = $container->getDefinition(MessengerLoggingMiddleware::class);
        } catch (Throwable $exception) {
            self::assertInstanceOf(ServiceNotFoundException::class, $exception);
            self::assertSame(
                'You have requested a non-existent service "SentryMonologAdapter\Messenger\Middleware\MessengerLoggingMiddleware".',
                $exception->getMessage()
            );
        }
        //phpcs:disable Generic.Files.LineLength
    }

    public function testWithFullConfig(): void
    {
        $container = $this->createContainerFromFixture('filled_bundle_config');

        $monologHandlerDecoratorDefinition = $container->getDefinition(
            'sentry_monolog_adapter.monolog_handler_decorator'
        );
        self::assertSame(MonologHandlerDecorator::class, $monologHandlerDecoratorDefinition->getClass());
        self::assertSame(Handler::class, $monologHandlerDecoratorDefinition->getDecoratedService()[0]);

        $messengerLoggingMiddlewareDefinition = $container->getDefinition(
            'sentry_monolog_adapter.messenger_logging_middleware'
        );

        self::assertSame(MessengerLoggingMiddleware::class, $messengerLoggingMiddlewareDefinition->getClass());
        self::assertSame(
            'monolog.logger',
            (string) $messengerLoggingMiddlewareDefinition->getArgument('$logger')
        );
        self::assertSame(
            'sentry_monolog_adapter.logging_strategy',
            (string) $messengerLoggingMiddlewareDefinition->getArgument('$loggingStrategy')
        );
        self::assertSame(
            'sentry_monolog_adapter.log_after_position_strategy',
            (string) $container->getAlias('sentry_monolog_adapter.logging_strategy')
        );

        $loggingStrategyDefinition = $container->getDefinition(
            'sentry_monolog_adapter.log_after_position_strategy'
        );
        self::assertSame(LogAfterPositionStrategy::class, $loggingStrategyDefinition->getClass());
        self::assertSame(
            2,
            $loggingStrategyDefinition->getArgument('$position')
        );
    }

    private function createContainerFromFixture(string $fixtureFile): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->registerExtension(new SentryMonologAdapterExtension());
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $this->loadFixture($container, $fixtureFile);

        $container->compile();

        return $container;
    }

    protected function loadFixture(ContainerBuilder $container, string $fixtureFile): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures'));
        $loader->load($fixtureFile . '.yaml');
    }
}
