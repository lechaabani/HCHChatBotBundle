<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use HCH\ChatBotBundle\Exception\RateLimitException;

class RateLimitService
{
    public function __construct(
        private RequestStack $requestStack,
        private AdapterInterface $cache,
        private array $config
    ) {}

    public function checkRateLimit(): void
    {
        if (!$this->config['rate_limit']['enabled']) {
            return;
        }

        $session = $this->requestStack->getSession();
        $key = 'chatbot_rate_limit_' . $session->getId();
        
        $item = $this->cache->getItem($key);
        $attempts = $item->get() ?? ['count' => 0, 'reset' => time()];

        // Réinitialiser le compteur si le délai est dépassé
        if (time() > $attempts['reset']) {
            $attempts = ['count' => 0, 'reset' => time() + $this->config['rate_limit']['window']];
        }

        if ($attempts['count'] >= $this->config['rate_limit']['max_attempts']) {
            throw new RateLimitException(sprintf(
                'Limite de requêtes atteinte. Réessayez dans %d secondes.',
                $attempts['reset'] - time()
            ));
        }

        $attempts['count']++;
        $item->set($attempts);
        $this->cache->save($item);
    }
} 