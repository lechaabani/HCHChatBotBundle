<?php

namespace HCH\ChatBotBundle\Enum;

enum PlanType: string
{
    case FREE = 'free';
    case PREMIUM = 'premium';
    case ENTERPRISE = 'enterprise';
} 