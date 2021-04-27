<?php

namespace SentryMonologAdapter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sentry_monolog_adapter');
        $root = $treeBuilder->getRootNode()->children();

        $this->addMonologHandlerSection($root);
        $this->addMessengerLoggingMiddlewareSection($root);

        return $treeBuilder;
    }

    private function addMonologHandlerSection(NodeBuilder $builder): void
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

    private function addMessengerLoggingMiddlewareSection(NodeBuilder $builder): void
    {
        $builder
            ->arrayNode('messenger_logging_middleware')
                ->children()
                    ->arrayNode('logging_strategies')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('id')->cannotBeEmpty()->end()
                                ->arrayNode('options')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->canBeEnabled()
            ->end()
        ;
    }
}
