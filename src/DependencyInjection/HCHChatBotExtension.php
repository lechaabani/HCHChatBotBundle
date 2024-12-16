<?php

namespace HCH\ChatBotBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HCHChatBotExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Configuration complète
        $container->setParameter('hch_chatbot.config', $config);
        
        // Configuration des providers
        $container->setParameter('hch_chatbot.providers', $config['providers']);
        foreach ($config['providers'] as $name => $providerConfig) {
            $container->setParameter(
                sprintf('hch_chatbot.providers.%s', $name),
                $providerConfig
            );
        }

        // Paramètres globaux
        $container->setParameter('hch_chatbot.default_provider', $config['default_provider'] ?? 'openai');
        $container->setParameter('hch_chatbot.fallback_chain', $config['fallback_chain'] ?? []);
        $container->setParameter('hch_chatbot.quota_manager.reset_period', $config['quota_manager']['reset_period'] ?? 'daily');

        // Cache configuration
        $container->setParameter('hch_chatbot.cache.default_ttl', $config['cache']['default_ttl']);
    }
} 