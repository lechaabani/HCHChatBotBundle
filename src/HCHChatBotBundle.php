<?php

namespace HCH\ChatBotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use HCH\ChatBotBundle\DependencyInjection\Compiler\ChatBotPluginPass;

class HCHChatBotBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        $container->addCompilerPass(new ChatBotPluginPass());
    }
} 