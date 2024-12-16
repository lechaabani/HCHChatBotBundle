<?php

namespace HCH\ChatBotBundle\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use HCH\ChatBotBundle\Provider\OpenAIProvider;
use HCH\ChatBotBundle\Service\DefaultPromptFormatter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use HCH\ChatBotBundle\Exception\ProviderException;

class OpenAIProviderTest extends TestCase
{
    private OpenAIProvider $provider;
    private $httpClient;
    private $promptFormatter;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->promptFormatter = $this->createMock(DefaultPromptFormatter::class);
        
        $this->provider = new OpenAIProvider(
            ['api_key' => 'test-key', 'model' => 'gpt-3.5-turbo'],
            $this->promptFormatter,
            $this->httpClient
        );
    }

    public function testSendMessage(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn(new class {
                public function toArray(): array
                {
                    return ['choices' => [['message' => ['content' => 'Test response']]]];
                }
            });

        $response = $this->provider->sendMessage('Test message');
        $this->assertEquals('Test response', $response);
    }

    public function testValidateConfiguration(): void
    {
        $this->expectException(ProviderException::class);
        $provider = new OpenAIProvider(
            [],
            $this->promptFormatter,
            $this->httpClient
        );
        $provider->validateConfiguration();
    }
} 