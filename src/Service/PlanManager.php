<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Enum\PlanType;
use HCH\ChatBotBundle\Exception\PlanLimitException;

class PlanManager
{
    private const PLAN_LIMITS = [
        PlanType::FREE => [
            'messages_per_day' => 50,
            'max_tokens' => 500,
            'features' => [
                'basic_chat' => true,
                'history' => false,
                'file_upload' => false,
                'custom_training' => false
            ]
        ],
        PlanType::PREMIUM => [
            'messages_per_day' => 1000,
            'max_tokens' => 2000,
            'features' => [
                'basic_chat' => true,
                'history' => true,
                'file_upload' => true,
                'custom_training' => false
            ]
        ],
        PlanType::ENTERPRISE => [
            'messages_per_day' => -1, // illimité
            'max_tokens' => 4000,
            'features' => [
                'basic_chat' => true,
                'history' => true,
                'file_upload' => true,
                'custom_training' => true
            ]
        ]
    ];

    public function __construct(
        private UsageTracker $usageTracker,
        private array $config
    ) {}

    public function checkMessageLimit(string $userId, PlanType $planType): void
    {
        $dailyCount = $this->usageTracker->getDailyMessageCount($userId);
        $limit = self::PLAN_LIMITS[$planType]['messages_per_day'];

        if ($limit !== -1 && $dailyCount >= $limit) {
            throw new PlanLimitException(
                "Limite de messages quotidienne atteinte pour votre plan {$planType->value}"
            );
        }
    }

    public function hasFeature(PlanType $planType, string $feature): bool
    {
        return self::PLAN_LIMITS[$planType]['features'][$feature] ?? false;
    }

    public function getMaxTokens(PlanType $planType): int
    {
        return self::PLAN_LIMITS[$planType]['max_tokens'];
    }
} 