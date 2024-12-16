<?php

namespace App\ChatBotBundle\Service;

use App\ChatBotBundle\Provider\LLMProviderInterface;

class LLMProviderFactory
{
    private $providers;
    private $config;

    public function __construct(iterable $providers, array $config)
    {
        $this->providers = $providers;
        $this->config = $config;
    }

    public function getProvider(?string $name = null): LLMProviderInterface
    {
        $providerName = $name ?? $this->config['llm_provider']['default'];

        foreach ($this->providers as $provider) {
            if ($provider->supports($providerName)) {
                return $provider;
            }
        }

        throw new \RuntimeException(sprintf('No LLM provider found for "%s"', $providerName));
    }
} 