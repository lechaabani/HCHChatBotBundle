<?php

namespace HCH\ChatBotBundle\Plugin;

class PluginRegistry
{
    private $plugins = [];

    public function registerPlugin(ChatBotPluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    public function getPlugin(string $name): ?ChatBotPluginInterface
    {
        return $this->plugins[$name] ?? null;
    }

    public function getEnabledPlugins(): array
    {
        return array_filter($this->plugins, fn($plugin) => $plugin->isEnabled());
    }
} 