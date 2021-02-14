<?php

namespace SentryMonologAdapter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sentry_monolog_adapter');
        $root = $treeBuilder->getRootNode()->children();;

        $this->addMonologHandlerSection($root);
        $this->addMessengerLoggingMiddlewareSection($root);

        return $treeBuilder;
    }

    private function addMonologHandlerSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('monolog_handler_decorator')
                ->children()
                    ->arrayNode('processors')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
                ->canBeEnabled()
            ->end()
        ;
    }

    private function addMessengerLoggingMiddlewareSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('messenger_logging_middleware')
                ->children()
                    ->arrayNode('logging_strategy')
                        ->children()
                            ->scalarNode('id')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->canBeEnabled()
            ->end()
        ;
    }
}