<?php

namespace HCH\ChatBotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HCHChatBotBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.yaml');

        // Configuration par défaut
        $container->setParameter('hch_chatbot.providers', [
            'openai' => [
                'api_key' => '%env(OPENAI_API_KEY)%',
                'model' => 'gpt-3.5-turbo'
            ]
        ]);
        
        $container->setParameter('hch_chatbot.default_provider', 'openai');
        $container->setParameter('hch_chatbot.cache.default_ttl', 3600);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = $this->createContainerExtension();
        }
        return $this->extension;
    }
} 