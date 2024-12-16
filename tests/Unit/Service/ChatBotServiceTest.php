<?php

namespace HCH\ChatBotBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use HCH\ChatBotBundle\Service\ChatBotService;
use HCH\ChatBotBundle\Factory\LLMProviderFactory;
use HCH\ChatBotBundle\Service\FallbackManager;
use HCH\ChatBotBundle\Service\QuotaManager;
use HCH\ChatBotBundle\Provider\OpenAIProvider;
use PHPUnit\Framework\MockObject\MockObject;

class ChatBotServiceTest extends TestCase
{
    private ChatBotService $chatBot;
    private MockObject $providerFactory;
    private MockObject $fallbackManager;
    private MockObject $quotaManager;
    private MockObject $provider;

    protected function setUp(): void
    {
        $this->providerFactory = $this->createMock(LLMProviderFactory::class);
        $this->fallbackManager = $this->createMock(FallbackManager::class);
        $this->quotaManager = $this->createMock(QuotaManager::class);
        $this->provider = $this->createMock(OpenAIProvider::class);

        $this->chatBot = new ChatBotService(
            $this->providerFactory,
            $this->fallbackManager,
            $this->quotaManager,
            'openai'
        );
    }

    public function testSendMessage(): void
    {
        $message = 'Test message';
        $expectedResponse = 'Test response';

        $this->providerFactory
            ->expects($this->once())
            ->method('createProvider')
            ->with('openai')
            ->willReturn($this->provider);

        $this->provider
            ->expects($this->once())
            ->method('sendMessage')
            ->with($message)
            ->willReturn($expectedResponse);

        $response = $this->chatBot->sendMessage($message);
        $this->assertEquals($expectedResponse, $response);
    }
} 