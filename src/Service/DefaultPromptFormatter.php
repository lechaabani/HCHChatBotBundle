<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Contract\PromptFormatterInterface;

class DefaultPromptFormatter implements PromptFormatterInterface
{
    public function formatPrompt(string $message, array $context = []): array
    {
        $messages = [];

        if (isset($context['system'])) {
            $messages[] = [
                'role' => 'system',
                'content' => $context['system']
            ];
        }

        if (isset($context['history']) && is_array($context['history'])) {
            foreach ($context['history'] as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        return $messages;
    }
} 