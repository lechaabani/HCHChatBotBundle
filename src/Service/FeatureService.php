<?php

namespace App\ChatBotBundle\Service;

class FeatureService
{
    private $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function isPremium(): bool
    {
        return $this->version === 'premium';
    }

    public function canUseFeature(string $feature): bool
    {
        $premiumFeatures = [
            'history_export',
            'advanced_translation',
            'custom_models',
            'plugins'
        ];

        if (in_array($feature, $premiumFeatures)) {
            return $this->isPremium();
        }

        return true;
    }
} 