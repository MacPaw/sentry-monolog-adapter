<?php


namespace SentrySymfony\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sentry_symfony');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('messenger_logging_middleware')->canBeEnabled()->end()
                ->arrayNode('monolog_handler_decorator')->canBeEnabled()->end()
            ->end();

        return $treeBuilder;
    }
}