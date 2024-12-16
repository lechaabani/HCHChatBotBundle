# Utilisation du Bundle

## Installation

```bash
composer require hch/chat-bot-bundle
```

## Configuration

```yaml
# config/packages/hch_chatbot.yaml
hch_chatbot:
    providers:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
            model: 'gpt-3.5-turbo'
    default_provider: 'openai'
```

## Utilisation basique

```php
class MyController
{
    public function index(ChatBotService $chatBot)
    {
        $response = $chatBot->sendMessage('Bonjour!');
        
        // Avec contexte
        $response = $chatBot->sendMessage('Traduis en anglais', [
            'template' => 'translation',
            'language' => 'anglais'
        ]);
        
        // Avec provider spécifique
        $response = $chatBot->sendMessage('Hello', [], 'claude');
    }
}
``` 