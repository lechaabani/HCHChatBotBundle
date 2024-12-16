<?php

namespace HCH\ChatBotBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['startSession', 10],
        ];
    }

    public function startSession(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $session = $this->requestStack->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
    }
} 