<?php

namespace HCH\ChatBotBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
            // Doit s'exécuter avant le firewall et autres listeners
            KernelEvents::REQUEST => ['onKernelRequest', 128]
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Vérifie si la session est déjà initialisée
        if ($request->hasSession()) {
            return;
        }

        try {
            $session = $this->requestStack->getSession();
            if (!$session->isStarted() && !headers_sent()) {
                $session->start();
            }
            $request->setSession($session);
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
        }
    }
} 