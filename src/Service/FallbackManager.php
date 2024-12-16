<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Contract\LLMProviderInterface;
use HCH\ChatBotBundle\Factory\LLMProviderFactory;
use HCH\ChatBotBundle\Exception\ProviderException;

class FallbackManager
{
    private array $fallbackChain;
    private LLMProviderFactory $providerFactory;

    public function __construct(
        LLMProviderFactory $providerFactory,
        array $fallbackChain
    ) {
        $this->providerFactory = $providerFactory;
        $this->fallbackChain = $fallbackChain;
    }

    public function executeWithFallback(string $message, array $context = []): string
    {
        $lastException = null;

        foreach ($this->fallbackChain as $providerName) {
            try {
                $provider = $this->providerFactory->createProvider($providerName, []);
                return $provider->sendMessage($message, $context);
            } catch (\Exception $e) {
                $lastException = $e;
                continue;
            }
        }

        throw new ProviderException(
            'All providers failed. Last error: ' . $lastException->getMessage(),
            0,
            $lastException
        );
    }

    public function handleFailure(string $message): string
    {
        // Message par défaut en cas d'échec
        return "Désolé, je ne peux pas traiter votre demande pour le moment. Veuillez réessayer plus tard.";
    }

    public function getFallbackProviders(): array
    {
        return $this->fallbackChain;
    }
} 