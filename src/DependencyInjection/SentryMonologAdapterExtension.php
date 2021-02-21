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

    private function loadMessengerLoggingMiddleware(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        if ($config['messenger_logging_middleware']['enabled']) {
            $loader->load('messenger_logging_middleware.xml');

            if (isset($config['messenger_logging_middleware']['logging_strategy'])) {
                $container->setAlias(
                    'sentry_monolog_adapter.logging_strategy',
                    $config['messenger_logging_middleware']['logging_strategy']['id']
                );

                $loggingStrategyDefinition = $container->findDefinition('sentry_monolog_adapter.logging_strategy');
                $loggingStrategyDefinition->addArgument(
                    $config['messenger_logging_middleware']['logging_strategy']['argument']
                );
            }
        }
    }

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
