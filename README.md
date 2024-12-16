# HCHChatBotBundle

Bundle Symfony pour intégrer un chatbot IA avec gestion des plans Free/Premium, support multi-LLM et interface utilisateur prête à l'emploi.

## Caractéristiques

- 🤖 Support Multiple LLMs :
  - OpenAI GPT
  - Claude Anthropic
  - LLaMA
  - Mistral AI
  - Google PaLM
  - Cohere
  - LLMs personnalisés
- 💬 Interface de chat prête à l'emploi :
  - Widget intégrable
  - Personnalisation complète
  - Responsive design
  - Mode sombre/clair
- 🎨 Thèmes et templates :
  - Templates Twig personnalisables
  - Styles CSS modifiables
  - Hooks pour personnalisation
- 🔌 Intégration facile :
  - Installation en une ligne
  - Auto-configuration
  - Widgets prêts à l'emploi
- 📊 Analytics et monitoring :
  - Dashboard d'administration
  - Statistiques d'utilisation
  - Monitoring des coûts
  - Alertes et notifications
- 💎 Gestion Free/Premium
- 🔌 Architecture modulaire

## Installation

```bash
composer require hch/chat-bot-bundle
```

## Intégration rapide

1. Ajouter le widget dans votre template Twig :

```twig
{# templates/base.html.twig #}
{{ render_chatbot_widget() }}
```

2. Ou utiliser le hook JavaScript :

```html
<!-- Dans votre layout -->
<div id="chatbot-container"></div>

<script>
    HCHChatBot.init({
        container: '#chatbot-container',
        theme: 'light',
        position: 'bottom-right'
    });
</script>
```

## Configuration

```yaml
# config/packages/hch_chatbot.yaml
hch_chatbot:
    interface:
        enabled: true
        theme: 'default'
        position: 'bottom-right'
        templates:
            widget: '@HCHChatBot/widget.html.twig'
            messages: '@HCHChatBot/messages.html.twig'
    providers:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
            model: 'gpt-3.5-turbo'
    admin:
        enabled: true
        dashboard: true
```

## Personnalisation de l'interface

1. Surcharger les templates :

```twig
{# templates/bundles/HCHChatBotBundle/widget.html.twig #}
{% extends '@!HCHChatBot/widget.html.twig' %}

{% block chatbot_header %}
    <div class="custom-header">
        {{ parent() }}
    </div>
{% endblock %}
```

2. Personnaliser via JavaScript :

```javascript
HCHChatBot.customize({
    headerText: 'Mon Assistant',
    primaryColor: '#007bff',
    hooks: {
        beforeSend: (message) => {
            // Personnalisation avant envoi
            return message;
        },
        afterResponse: (response) => {
            // Personnalisation après réponse
            return response;
        }
    }
});
```

## Hooks disponibles

- `beforeSend`: Modification du message avant envoi
- `afterResponse`: Traitement de la réponse
- `onError`: Gestion personnalisée des erreurs
- `onInit`: Actions à l'initialisation
- `onOpen`: Actions à l'ouverture du chat
- `onClose`: Actions à la fermeture du chat

## Documentation

[Documentation complète](docs/index.md)

## Support

Pour toute question ou assistance :
- GitHub Issues : [github.com/lechaabani/hch-chatbot-bundle/issues](https://github.com/lechaabani/hch-chatbot-bundle/issues)
- Email : chaabani.hammadi@gmail.com

## License

MIT

## Author

Hamadi CHAABANI