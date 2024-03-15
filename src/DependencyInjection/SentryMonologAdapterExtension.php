<?php

declare(strict_types=1);

namespace SentryMonologAdapter\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SentryMonologAdapterExtension extends Extension
{
    /**
     * @param array<string, mixed> $configs
     *
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->loadMessengerLoggingMiddleware($config, $loader, $container);
        $this->loadMonologHandlerDecorator($config, $loader, $container);
    }

    /**
     * @param array<string, array<mixed>> $config
     */
    private function loadMessengerLoggingMiddleware(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        if ($config['messenger_logging_middleware']['enabled']) {
            $loader->load('messenger_logging_middleware.xml');

            $messengerLoggingMiddlewareDefinition = $container
                ->findDefinition('sentry_monolog_adapter.messenger_logging_middleware');
            foreach ($config['messenger_logging_middleware']['logging_strategies'] as $loggingStrategyConfig) {
                $loggingStrategyDefinition = $container->findDefinition($loggingStrategyConfig['id']);
                foreach ($loggingStrategyConfig['options'] as $key => $value) {
                    $loggingStrategyDefinition->setArgument('$' . $key, $value);
                }

                $messengerLoggingMiddlewareDefinition->addMethodCall(
                    'addLoggingStrategy',
                    [$loggingStrategyDefinition]
                );
            }
        }
    }

    /**
     * @param array<string, array<mixed>> $config
     */
    private function loadMonologHandlerDecorator(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        if ($config['monolog_handler_decorator']['enabled']) {
            $loader->load('monolog_handler_decorator.xml');

            if (isset($config['monolog_handler_decorator']['processors'])) {
                foreach ($config['monolog_handler_decorator']['processors'] as $processorClassName) {
                    $container
                        ->register($processorClassName)
                        ->setPublic(false)
                        ->addTag('monolog.processor', ['handler' => 'sentry']);
                }
            }
        }
    }
}
