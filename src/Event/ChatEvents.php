<?php

namespace HCH\ChatBotBundle\Event;

final class ChatEvents
{
    public const PRE_MESSAGE = 'hch_chatbot.pre_message';
    public const POST_MESSAGE = 'hch_chatbot.post_message';
    public const ERROR = 'hch_chatbot.error';
} 