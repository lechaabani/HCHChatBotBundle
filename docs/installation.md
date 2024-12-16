# Installation du HCHChatBotBundle

## Étapes détaillées

1. Installation via Composer
```bash
composer require hch/chat-bot-bundle
```

2. Configuration du bundle
```yaml
# config/packages/hch_chatbot.yaml
hch_chatbot:
    llm_provider:
        default: 'openai'
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
```

3. Configuration des variables d'environnement
```bash
echo "OPENAI_API_KEY=your_api_key_here" >> .env.local
```

4. Installation des assets
```bash
php bin/console assets:install
```

5. Mise à jour de la base de données (si nécessaire)
```bash
php bin/console doctrine:schema:update --force
```

## Vérification de l'installation

Pour vérifier que tout est correctement installé :
```bash
php bin/console hch:HCHChatBotBundle:check
```