# Providers LLM

## Ajouter un nouveau Provider

Pour ajouter un nouveau provider LLM :

1. Créez une classe qui étend `AbstractLLMProvider`
2. Implémentez les méthodes requises
3. Enregistrez le provider dans la configuration

Exemple :
```php
class CustomLLMProvider extends AbstractLLMProvider
{
    public function sendMessage(string $message, array $context = []): string
    {
        // Implémentation de l'envoi du message
        return $this->executeRequest($this->formatPrompt($message, $context));
    }
    
    public function getModelName(): string
    {
        return 'custom_llm';
    }

    public function getTokenCount(string $text): int
    {
        // Implémentation du comptage de tokens
    }

    protected function executeRequest(array $formattedPrompt): string
    {
        // Implémentation de la requête API
    }

    public function validateConfiguration(): bool
    {
        // Validation de la configuration
    }
}
```

## Configuration

Chaque provider peut être configuré dans `config/packages/hch_chatbot.yaml` :

```yaml
hch_chatbot:
    providers:
        custom_llm:
            api_key: '%env(CUSTOM_LLM_API_KEY)%'
            model: 'custom-model'
            limits:
                daily_requests: 1000
                tokens_per_request: 4000
            fallback: 'openai'
```

## Gestion des quotas

Les quotas sont gérés automatiquement pour chaque provider via le QuotaManager. 
Vous pouvez configurer :
- Le nombre maximum de requêtes par jour
- La limite de tokens par requête
- La période de réinitialisation des quotas

## Système de Fallback

Le système de fallback permet de basculer automatiquement vers un autre provider en cas d'erreur :

```yaml
hch_chatbot:
    fallback_chain: ['openai', 'claude', 'mistral']
```

## Formatage des Prompts

Vous pouvez personnaliser le formatage des prompts :

```php
$promptFormatter->addSystemPrompt('Tu es un assistant francophone...');
$promptFormatter->addTemplate('translation', 'Traduis en {{language}}: {{message}}');
```

## Gestion des Erreurs

Les providers doivent gérer les exceptions suivantes :
- `ProviderException` : Erreurs générales du provider
- `QuotaExceededException` : Dépassement de quota
- `ConfigurationException` : Erreurs de configuration 