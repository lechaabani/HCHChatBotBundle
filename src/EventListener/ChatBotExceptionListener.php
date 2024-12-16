<?php

namespace HCH\ChatBotBundle\EventListener;

use HCH\ChatBotBundle\Exception\ChatBotException;
use HCH\ChatBotBundle\Service\NotificationService;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class ChatBotExceptionListener
{
    public function __construct(
        private NotificationService $notificationService,
        private LoggerInterface $logger
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        if (!$exception instanceof ChatBotException) {
            return;
        }

        $this->logger->error('ChatBot Error', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $this->notificationService->sendErrorAlert(
            $exception->getMessage(),
            ['class' => get_class($exception)]
        );

        $event->setResponse(new JsonResponse([
            'error' => true,
            'message' => $exception->getMessage()
        ], 500));
    }
} 