<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Contract\PromptFormatterInterface;

class DefaultPromptFormatter implements PromptFormatterInterface
{
    private array $systemPrompts = [];
    private array $templates = [];

    public function format(string $message, array $context = []): array
    {
        $messages = [];

        // Ajouter les prompts système
        foreach ($this->systemPrompts as $systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $this->replaceVariables($systemPrompt, $context)
            ];
        }

        // Ajouter l'historique si présent
        if (isset($context['history']) && is_array($context['history'])) {
            $messages = array_merge($messages, $context['history']);
        }

        // Ajouter le message utilisateur
        if (isset($context['template']) && isset($this->templates[$context['template']])) {
            $message = $this->replaceVariables($this->templates[$context['template']], ['message' => $message] + $context);
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        return $messages;
    }

    public function addSystemPrompt(string $prompt): void
    {
        $this->systemPrompts[] = $prompt;
    }

    public function addTemplate(string $name, string $template): void
    {
        $this->templates[$name] = $template;
    }

    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_scalar($value)) {
                $text = str_replace('{{' . $key . '}}', (string) $value, $text);
            }
        }
        return $text;
    }
} 