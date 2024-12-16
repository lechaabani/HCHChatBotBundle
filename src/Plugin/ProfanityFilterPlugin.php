<?php

namespace HCH\ChatBotBundle\Plugin;

class ProfanityFilterPlugin extends AbstractChatBotPlugin
{
    private array $badWords;
    private string $replacement;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->badWords = $config['bad_words'] ?? [];
        $this->replacement = $config['replacement'] ?? '***';
        $this->priority = 100; // Haute priorité pour filtrer en premier
    }

    public function getName(): string
    {
        return 'profanity_filter';
    }

    public function processMessage(string $message, array $context = []): string
    {
        foreach ($this->badWords as $word) {
            $message = preg_replace(
                '/\b' . preg_quote($word, '/') . '\b/i',
                $this->replacement,
                $message
            );
        }

        return $message;
    }
} 