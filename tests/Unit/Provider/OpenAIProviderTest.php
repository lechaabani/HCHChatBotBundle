<?php

namespace HCH\ChatBotBundle\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use HCH\ChatBotBundle\Provider\OpenAIProvider;
use HCH\ChatBotBundle\Contract\PromptFormatterInterface;
use HCH\ChatBotBundle\Exception\ProviderException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenAIProviderTest extends TestCase
{
    private OpenAIProvider $provider;
    private $httpClient;
    private $promptFormatter;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->promptFormatter = $this->createMock(PromptFormatterInterface::class);

        $this->provider = new OpenAIProvider(
            ['api_key' => 'test-key', 'model' => 'gpt-3.5-turbo'],
            $this->promptFormatter,
            $this->httpClient
        );
    }

    public function testSendMessage(): void
    {
        $this->promptFormatter
            ->expects($this->once())
            ->method('formatPrompt')
            ->willReturn(['messages' => []]);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')
            ->willReturn([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Test response'
                        ]
                    ]
                ]
            ]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $result = $this->provider->sendMessage('Test message');
        $this->assertEquals('Test response', $result);
    }

    public function testValidateConfiguration(): void
    {
        $this->assertTrue($this->provider->validateConfiguration());

        $providerWithoutKey = new OpenAIProvider(
            ['model' => 'gpt-3.5-turbo'],
            $this->promptFormatter,
            $this->httpClient
        );

        $this->expectException(ProviderException::class);
        $providerWithoutKey->validateConfiguration();
    }
} 