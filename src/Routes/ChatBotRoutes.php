<?php

namespace HCH\ChatBotBundle\Routes;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ChatBotRoutes extends Loader
{
    private bool $isLoaded = false;

    public function load(mixed $resource, string $type = null): RouteCollection
    {
        if ($this->isLoaded) {
            throw new \RuntimeException('Do not add the same loader twice');
        }

        $routes = new RouteCollection();

        $resource = '@HCHChatBotBundle/Resources/config/routes/routes.yaml';
        $type = 'yaml';

        $importedRoutes = $this->import($resource, $type);
        $routes->addCollection($importedRoutes);

        $this->isLoaded = true;

        return $routes;
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return 'hch_chatbot' === $type;
    }
} 