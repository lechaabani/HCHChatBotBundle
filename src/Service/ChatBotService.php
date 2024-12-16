<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Factory\LLMProviderFactory;
use HCH\ChatBotBundle\Exception\ProviderException;

class ChatBotService
{
    private LLMProviderFactory $providerFactory;
    private FallbackManager $fallbackManager;
    private QuotaManager $quotaManager;
    private string $defaultProvider;

    public function __construct(
        LLMProviderFactory $providerFactory,
        FallbackManager $fallbackManager,
        QuotaManager $quotaManager,
        string $defaultProvider
    ) {
        $this->providerFactory = $providerFactory;
        $this->fallbackManager = $fallbackManager;
        $this->quotaManager = $quotaManager;
        $this->defaultProvider = $defaultProvider;
    }

    public function sendMessage(string $message, array $context = []): string
    {
        $provider = $context['provider'] ?? $this->defaultProvider;
        
        try {
            $llmProvider = $this->providerFactory->createProvider($provider, $context);
            return $llmProvider->sendMessage($message, $context);
        } catch (ProviderException $e) {
            return $this->fallbackManager->handleFailure($message, $context);
        }
    }
} 