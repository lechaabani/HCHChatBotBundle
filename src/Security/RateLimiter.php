<?php

namespace HCH\ChatBotBundle\Security;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class RateLimiter
{
    private $limiter;

    public function __construct(RateLimiterFactory $factory)
    {
        $this->limiter = $factory;
    }

    public function checkRateLimit(string $identifier): void
    {
        $limiter = $this->limiter->create($identifier);
        
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }
    }
} 