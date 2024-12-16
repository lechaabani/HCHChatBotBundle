<?php

namespace HCH\ChatBotBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use HCH\ChatBotBundle\Service\ChatBotService;
use HCH\ChatBotBundle\Provider\OpenAIProvider;
use HCH\ChatBotBundle\Service\TranslationService;
use HCH\ChatBotBundle\Service\Analytics\AnalyticsService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class ChatBotServiceTest extends TestCase
{
    private ChatBotService $service;
    private $providerMock;
    private $translationService;
    private $analyticsService;
    private $dispatcher;
    private $logger;

    protected function setUp(): void
    {
        $this->providerMock = $this->createMock(OpenAIProvider::class);
        $this->translationService = $this->createMock(TranslationService::class);
        $this->analyticsService = $this->createMock(AnalyticsService::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new ChatBotService(
            $this->providerMock,
            $this->translationService,
            $this->analyticsService,
            $this->dispatcher,
            $this->logger,
            ['websocket' => ['enabled' => true]]
        );
    }

    public function testProcessMessage(): void
    {
        $message = "Test message";
        $expectedResponse = "Expected response";

        $this->providerMock
            ->expects($this->once())
            ->method('getResponse')
            ->with($message)
            ->willReturn($expectedResponse);

        $response = $this->service->processMessage($message);
        $this->assertEquals($expectedResponse, $response);
    }

    // Autres tests...
} 