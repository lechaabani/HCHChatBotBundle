<?php

namespace HCH\ChatBotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HCHChatBotBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Configuration par défaut si non fournie
        $config = array_merge([
            'providers' => [
                'openai' => [
                    'api_key' => '%env(OPENAI_API_KEY)%',
                    'model' => 'gpt-3.5-turbo'
                ]
            ],
            'default_provider' => 'openai',
            'cache' => [
                'default_ttl' => 3600
            ]
        ], $config);

        // Charge les services
        $container->import('../config/services.yaml');
        
        // Configure les paramètres
        $container->parameters()->set('hch_chatbot.providers', $config['providers']);
        $container->parameters()->set('hch_chatbot.default_provider', $config['default_provider']);
        $container->parameters()->set('hch_chatbot.cache.default_ttl', $config['cache']['default_ttl']);
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
} 