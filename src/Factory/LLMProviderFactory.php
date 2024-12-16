<?php

namespace HCH\ChatBotBundle\Factory;

use HCH\ChatBotBundle\Contract\LLMProviderInterface;
use HCH\ChatBotBundle\Exception\UnsupportedLLMException;

class LLMProviderFactory
{
    private array $providers = [];
    
    public function registerProvider(string $name, string $providerClass): void
    {
        $this->providers[$name] = $providerClass;
    }
    
    public function createProvider(string $name, array $config): LLMProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new UnsupportedLLMException("LLM provider '$name' not supported");
        }
        
        $providerClass = $this->providers[$name];
        return new $providerClass($config);
    }
} 