<?php

namespace HCH\ChatBotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hch_chatbot');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('providers')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('model')->defaultValue('gpt-3.5-turbo')->end()
                            ->arrayNode('limits')
                                ->children()
                                    ->integerNode('daily_requests')->defaultValue(1000)->end()
                                    ->integerNode('tokens_per_request')->defaultValue(4096)->end()
                                ->end()
                            ->end()
                            ->scalarNode('fallback')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_provider')
                    ->defaultValue('openai')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('fallback_chain')
                    ->scalarPrototype()->end()
                    ->defaultValue(['openai', 'claude', 'mistral'])
                ->end()
                ->arrayNode('quota_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('storage')->defaultValue('redis')->end()
                        ->scalarNode('reset_period')->defaultValue('daily')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
} 