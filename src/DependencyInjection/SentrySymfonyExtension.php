<?php

declare(strict_types=1);

namespace SentrySymfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SentrySymfonyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->loadMessengerLoggingMiddleware($config, $loader);
        $this->loadMonologHandlerDecorator($config, $loader);
    }

    private function loadMessengerLoggingMiddleware(
        array $config,
        XmlFileLoader $loader
    ): void {
        if ($config['messenger_logging_middleware']['enabled']) {
            $loader->load('messenger_logging_middleware.xml');
        }
    }

    private function loadMonologHandlerDecorator(
        array $config,
        XmlFileLoader $loader
    ): void {
        if ($config['monolog_handler_decorator']['enabled']) {
            $loader->load('monolog_handler_decorator.xml');
        }
    }
}