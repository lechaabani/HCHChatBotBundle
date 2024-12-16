<?php

namespace HCH\ChatBotBundle\Provider;

use HCH\ChatBotBundle\Exception\ProviderException;

class ClaudeProvider extends AbstractLLMProvider
{
    private const TOKEN_LIMIT = 100000;

    protected function executeRequest(array $formattedPrompt): string
    {
        $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->config['api_key'],
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => [
                'model' => $this->config['model'],
                'messages' => $formattedPrompt,
                'max_tokens' => $this->config['max_tokens'] ?? 1000,
            ]
        ]);

        $data = $response->toArray();
        return $data['content'][0]['text'] ?? throw new ProviderException('Invalid Claude response format');
    }

    public function getModelCapabilities(): array
    {
        return [
            'max_tokens' => self::TOKEN_LIMIT,
            'supports_streaming' => true,
            'supports_functions' => false,
            'supports_vision' => false
        ];
    }
} 