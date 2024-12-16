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
    private ConversationManager $conversationManager;

    public function __construct(
        LLMProviderFactory $providerFactory,
        FallbackManager $fallbackManager,
        QuotaManager $quotaManager,
        string $defaultProvider,
        ConversationManager $conversationManager
    ) {
        $this->providerFactory = $providerFactory;
        $this->fallbackManager = $fallbackManager;
        $this->quotaManager = $quotaManager;
        $this->defaultProvider = $defaultProvider;
        $this->conversationManager = $conversationManager;
    }

    public function sendMessage(string $message, string $conversationId = null): string
    {
        $conversationId = $conversationId ?? uniqid('conv_');
        
        try {
            $provider = $this->defaultProvider;
            $llmProvider = $this->providerFactory->createProvider($provider);
            $response = $llmProvider->sendMessage($message);
            $this->conversationManager->addToHistory($conversationId, $message, $response);
            return $response;
        } catch (ProviderException $e) {
            return $this->fallbackManager->handleFailure($message);
        }
    }
} 