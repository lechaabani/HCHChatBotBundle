<?php

namespace HCH\ChatBotBundle\Factory;

use HCH\ChatBotBundle\Contract\LLMProviderInterface;
use HCH\ChatBotBundle\Exception\ProviderException;
use HCH\ChatBotBundle\Service\DefaultPromptFormatter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LLMProviderFactory
{
    private array $providers = [];
    private HttpClientInterface $httpClient;
    private DefaultPromptFormatter $promptFormatter;

    public function __construct(
        HttpClientInterface $httpClient,
        DefaultPromptFormatter $promptFormatter
    ) {
        $this->httpClient = $httpClient;
        $this->promptFormatter = $promptFormatter;
    }

    public function registerProvider(string $name, string $class): void
    {
        $this->providers[$name] = $class;
    }

    public function createProvider(string $name, array $config): LLMProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new ProviderException(sprintf('Provider "%s" not found', $name));
        }

        $class = $this->providers[$name];
        return new $class($config, $this->promptFormatter, $this->httpClient);
    }

    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }
} 