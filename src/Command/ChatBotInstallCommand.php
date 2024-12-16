<?php

namespace HCH\ChatBotBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

class ChatBotInstallCommand extends Command
{
    protected static $defaultName = 'hch:HCHChatBotBundle:install';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // 1. Vérification des prérequis
            $io->section('Vérification des prérequis');
            $this->checkRequirements($io);

            // 2. Création des tables en base de données
            $io->section('Configuration de la base de données');
            $this->setupDatabase($io);

            // 3. Configuration des providers
            $io->section('Configuration des providers');
            $this->configureProviders($io);

            // 4. Installation des assets
            $io->section('Installation des assets');
            $this->installAssets($io);

            // 5. Configuration du cache
            $io->section('Configuration du cache');
            $this->setupCache($io);

            // 6. Configuration des websockets
            $io->section('Configuration des websockets');
            $this->setupWebsockets($io);

            $io->success('ChatBot installé avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de l\'installation : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function checkRequirements(SymfonyStyle $io): void
    {
        $requirements = [
            'PHP Version >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Intl Extension' => extension_loaded('intl'),
            'JSON Extension' => extension_loaded('json')
        ];

        foreach ($requirements as $requirement => $satisfied) {
            if (!$satisfied) {
                throw new \RuntimeException("Prérequis non satisfait : $requirement");
            }
            $io->writeln("✓ $requirement");
        }
    }

    private function setupDatabase(SymfonyStyle $io): void
    {
        // Création/mise à jour du schéma de base de données
        $command = $this->getApplication()->find('doctrine:schema:update');
        $arguments = [
            '--force' => true,
            '--complete' => true
        ];
        $command->run(new ArrayInput($arguments), $output);
    }

    private function configureProviders(SymfonyStyle $io): void
    {
        // Configuration des providers
        $providers = ['openai', 'huggingface', 'custom'];
        $selectedProvider = $io->choice(
            'Sélectionnez le provider par défaut',
            $providers,
            'openai'
        );

        // Configuration spécifique au provider
        switch ($selectedProvider) {
            case 'openai':
                $apiKey = $io->askHidden('Entrez votre clé API OpenAI');
                // Sauvegarder la clé dans .env.local
                break;
            // ... autres cas
        }
    }

    private function installAssets(SymfonyStyle $io): void
    {
        // Installation des assets
        $command = $this->getApplication()->find('assets:install');
        $arguments = [
            '--symlink' => true,
            '--relative' => true
        ];
        $command->run(new ArrayInput($arguments), $output);
    }
} 