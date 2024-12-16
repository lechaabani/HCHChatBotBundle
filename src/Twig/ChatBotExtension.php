<?php

namespace HCH\ChatBotBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChatBotExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_chatbot_widget', [$this, 'renderWidget'], ['is_safe' => ['html']])
        ];
    }

    public function renderWidget(array $options = []): string
    {
        return '<div id="hch-chatbot-widget" data-options="' . htmlspecialchars(json_encode($options)) . '"></div>';
    }
} 