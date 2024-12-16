<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Security\Core\Security;
use HCH\ChatBotBundle\Exception\SecurityException;

class SecurityService
{
    public function __construct(
        private Security $security,
        private array $config
    ) {}

    public function validateAccess(): void
    {
        if ($this->config['security']['require_authentication'] && !$this->security->getUser()) {
            throw new SecurityException('Authentification requise pour accéder au HCHChatBotBundle');
        }

        if ($this->config['security']['required_roles']) {
            foreach ($this->config['security']['required_roles'] as $role) {
                if (!$this->security->isGranted($role)) {
                    throw new SecurityException('Accès non autorisé');
                }
            }
        }
    }

    public function sanitizeInput(string $input): string
    {
        // Nettoyage basique des entrées
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Filtres personnalisés selon la configuration
        if ($this->config['security']['input_filters']) {
            foreach ($this->config['security']['input_filters'] as $pattern => $replacement) {
                $input = preg_replace($pattern, $replacement, $input);
            }
        }

        return $input;
    }
} 