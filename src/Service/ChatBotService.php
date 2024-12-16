<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Factory\LLMProviderFactory;

class ChatBotService
{
    public function __construct(
        private LLMProviderFactory $providerFactory,
        private FallbackManager $fallbackManager,
        private QuotaManager $quotaManager,
        private string $defaultProvider
    ) {}

    public function sendMessage(string $message, array $context = [], ?string $provider = null): string
    {
        $providerName = $provider ?? $this->defaultProvider;
        
        try {
            $provider = $this->providerFactory->createProvider($providerName, []);
            return $provider->sendMessage($message, $context);
        } catch (\Exception $e) {
            return $this->fallbackManager->executeWithFallback($message, $context);
        }
    }
} 