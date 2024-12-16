<?php

namespace HCH\ChatBotBundle\Service;

use HCH\ChatBotBundle\Plugin\ChatBotPluginInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PluginManager
{
    private array $plugins = [];
    private ContainerInterface $pluginLocator;
    private array $config;

    public function __construct(ServiceLocator $pluginLocator, array $config)
    {
        $this->pluginLocator = $pluginLocator;
        $this->config = $config;
        $this->initializePlugins();
    }

    private function initializePlugins(): void
    {
        foreach ($this->config['plugins']['enabled_plugins'] as $pluginName) {
            if ($this->pluginLocator->has($pluginName)) {
                $this->addPlugin($this->pluginLocator->get($pluginName));
            }
        }
    }

    public function addPlugin(ChatBotPluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = $plugin;
        $this->sortPlugins();
    }

    public function removePlugin(string $name): void
    {
        unset($this->plugins[$name]);
    }

    public function getPlugin(string $name): ?ChatBotPluginInterface
    {
        return $this->plugins[$name] ?? null;
    }

    public function processMessage(string $message, array $context = []): string
    {
        foreach ($this->getEnabledPlugins() as $plugin) {
            $message = $plugin->processMessage($message, $context);
        }
        return $message;
    }

    public function getEnabledPlugins(): array
    {
        return array_filter($this->plugins, fn($plugin) => $plugin->isEnabled());
    }

    private function sortPlugins(): void
    {
        uasort($this->plugins, function($a, $b) {
            return $b->getPriority() <=> $a->getPriority();
        });
    }
} 