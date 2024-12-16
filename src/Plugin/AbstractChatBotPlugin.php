<?php

namespace HCH\ChatBotBundle\Plugin;

abstract class AbstractChatBotPlugin implements ChatBotPluginInterface
{
    protected bool $enabled = true;
    protected int $priority = 0;
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }
} 