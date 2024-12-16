<?php

namespace HCH\ChatBotBundle\Service;

class FeatureService
{
    private array $features;

    public function __construct(array $features = [])
    {
        $this->features = $features;
    }

    public function isEnabled(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }
} 