<?php

namespace HCH\ChatBotBundle\Provider;

use HCH\ChatBotBundle\Exception\ProviderException;

class PaLMProvider extends AbstractLLMProvider
{
    private const TOKEN_LIMIT = 8192;

    protected function executeRequest(array $formattedPrompt): string
    {
        $response = $this->httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/chat-bison-001:generateText', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => ['messages' => $formattedPrompt],
                'temperature' => $this->config['temperature'] ?? 0.7,
                'candidate_count' => 1,
            ]
        ]);

        $data = $response->toArray();
        return $data['candidates'][0]['content'] ?? throw new ProviderException('Invalid PaLM response format');
    }

    public function getModelCapabilities(): array
    {
        return [
            'max_tokens' => self::TOKEN_LIMIT,
            'supports_streaming' => false,
            'supports_functions' => true,
            'supports_vision' => false
        ];
    }
} 