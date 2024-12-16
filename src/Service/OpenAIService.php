<?php

namespace App\ChatBotBundle\Service;

use App\ChatBotBundle\Event\ChatMessageEvent;
use App\ChatBotBundle\Provider\LLMProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService implements LLMProviderInterface
{
    private $httpClient;
    private $apiKey;
    private $config;
    private $eventDispatcher;
    private $logger;
    private $pluginManager;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiKey,
        array $config,
        EventDispatcherInterface $eventDispatcher,
        ChatBotLogger $logger,
        PluginManager $pluginManager
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->pluginManager = $pluginManager;
    }

    public function getResponse(string $message, array $context = []): string
    {
        try {
            // Dispatch pre-process event
            $preEvent = new ChatMessageEvent($message, $context);
            $this->eventDispatcher->dispatch($preEvent, ChatMessageEvent::PRE_PROCESS);

            // Process message through plugins
            $processedMessage = $this->pluginManager->processMessage($message, $context);

            // Make API call
            $response = $this->makeApiCall($processedMessage);

            // Log the interaction
            $this->logger->logChat($message, $response, $this->getName(), $context);

            // Dispatch post-process event
            $postEvent = new ChatMessageEvent($message, $context);
            $postEvent->setResponse($response);
            $this->eventDispatcher->dispatch($postEvent, ChatMessageEvent::POST_PROCESS);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->logError($message, $e);
            $errorEvent = new ChatMessageEvent($message, $context);
            $this->eventDispatcher->dispatch($errorEvent, ChatMessageEvent::ERROR);
            throw $e;
        }
    }

    private function makeApiCall(string $message): string
    {
        $providerConfig = $this->config['llm_provider']['providers']['openai'];
        $endpoint = $providerConfig['api_url'] . $providerConfig['endpoints']['chat'];

        $response = $this->httpClient->request('POST', $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $providerConfig['model'],
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => $providerConfig['temperature']
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    public function getName(): string
    {
        return 'openai';
    }

    public function supports(string $provider): bool
    {
        return $provider === 'openai';
    }
} 