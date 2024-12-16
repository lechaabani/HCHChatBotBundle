<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator,
        private array $config
    ) {}

    public function validateMessage(string $message): void
    {
        if (empty(trim($message))) {
            throw new ValidationException('Le message ne peut pas être vide');
        }

        if (strlen($message) > $this->config['validation']['max_length']) {
            throw new ValidationException(sprintf(
                'Le message ne peut pas dépasser %d caractères',
                $this->config['validation']['max_length']
            ));
        }

        // Validation du contenu selon la configuration
        if ($this->config['validation']['forbidden_words']) {
            foreach ($this->config['validation']['forbidden_words'] as $word) {
                if (stripos($message, $word) !== false) {
                    throw new ValidationException('Le message contient du contenu non autorisé');
                }
            }
        }
    }

    public function validateContext(array $context): void
    {
        $allowedKeys = $this->config['validation']['allowed_context_keys'] ?? [];
        
        foreach ($context as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                throw new ValidationException(sprintf(
                    'La clé de contexte "%s" n\'est pas autorisée',
                    $key
                ));
            }
        }
    }
} 