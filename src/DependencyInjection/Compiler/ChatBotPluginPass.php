<?php

namespace HCH\ChatBotBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ChatBotPluginPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('hch_chatbot.plugin_manager')) {
            return;
        }

        $definition = $container->findDefinition('hch_chatbot.plugin_manager');
        $plugins = $container->findTaggedServiceIds('hch_chatbot.plugin');

        foreach ($plugins as $id => $tags) {
            $definition->addMethodCall('addPlugin', [new Reference($id)]);
        }
    }
} 