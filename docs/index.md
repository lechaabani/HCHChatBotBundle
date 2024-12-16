# HCHChatBotBundle Documentation

## Table des matières

1. [Installation](installation.md)
2. [Configuration](configuration.md)
3. [Utilisation](usage.md)
4. [Providers](providers.md)
5. [Monitoring](monitoring.md)
6. [Gestion des quotas](quotas.md)
7. [Système de fallback](fallback.md)
8. [Formatage des prompts](prompts.md)
9. [Gestion des erreurs](errors.md)

## Guides rapides

### Configuration minimale

```yaml
hch_chatbot:
    providers:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
```

### Utilisation basique

```php
$response = $chatBot->sendMessage('Bonjour!');
```

### Configuration avancée

```yaml
hch_chatbot:
    providers:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
            model: 'gpt-4'
            limits:
                daily_requests: 1000
        claude:
            api_key: '%env(CLAUDE_API_KEY)%'
            model: 'claude-2'
    
    fallback_chain: ['openai', 'claude']
    
    quota_manager:
        storage: 'redis'
        reset_period: 'daily'
```

## Exemples d'utilisation

Voir le dossier [examples/](../examples/) pour des exemples complets.