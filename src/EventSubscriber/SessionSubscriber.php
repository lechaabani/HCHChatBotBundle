<?php

namespace HCH\ChatBotBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Doit s'exécuter avant le firewall de sécurité (priorité 8)
            KernelEvents::REQUEST => ['startSession', 10],
        ];
    }

    public function startSession(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Démarre la session si elle n'est pas déjà démarrée
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }
} 