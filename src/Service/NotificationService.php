<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Channel\ChatChannel;
use Symfony\Component\Notifier\Channel\EmailChannel;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;

class NotificationService
{
    private $notifier;
    private $config;

    public function __construct(
        NotifierInterface $notifier,
        array $config
    ) {
        $this->notifier = $notifier;
        $this->config = $config;
    }

    public function sendNotification(
        string $message,
        array $channels = ['email', 'chat'],
        ?string $recipient = null,
        array $options = []
    ): void {
        $notification = (new Notification($message))
            ->channels($channels)
            ->importance($options['importance'] ?? Notification::IMPORTANCE_MEDIUM);

        if ($recipient) {
            $this->notifier->send($notification, new Recipient($recipient));
        } else {
            $this->notifier->send($notification);
        }
    }

    public function sendErrorAlert(string $error, array $context = []): void
    {
        $notification = (new Notification(
            'Erreur ChatBot: ' . $error,
            ['chat/slack', 'email']
        ))
        ->importance(Notification::IMPORTANCE_URGENT)
        ->content(sprintf(
            "Une erreur est survenue dans le ChatBot:\n%s\nContexte: %s",
            $error,
            json_encode($context, JSON_PRETTY_PRINT)
        ));

        foreach ($this->config['notifications']['admin_emails'] as $email) {
            $this->notifier->send($notification, new Recipient($email));
        }
    }

    public function sendChatAlert(string $message, string $channel = 'slack'): void
    {
        $chatMessage = new ChatMessage($message);
        
        if ($channel === 'slack') {
            $chatMessage->options([
                'color' => 'danger',
                'username' => 'ChatBot Alert',
                'icon_emoji' => ':warning:'
            ]);
        }

        $this->notifier->send($chatMessage);
    }

    public function sendDailyReport(array $stats): void
    {
        $emailMessage = (new EmailMessage())
            ->subject('Rapport quotidien ChatBot')
            ->html($this->generateReportHtml($stats));

        foreach ($this->config['notifications']['report_recipients'] as $email) {
            $this->notifier->send($emailMessage, new Recipient($email));
        }
    }

    private function generateReportHtml(array $stats): string
    {
        return <<<HTML
            <h1>Rapport ChatBot - {$stats['date']}</h1>
            <h2>Statistiques</h2>
            <ul>
                <li>Total conversations: {$stats['total_conversations']}</li>
                <li>Total messages: {$stats['total_messages']}</li>
                <li>Taux de réussite: {$stats['success_rate']}%</li>
            </ul>
            <h2>Top Utilisateurs</h2>
            <ul>
                {$this->generateUsersList($stats['top_users'])}
            </ul>
        HTML;
    }

    private function generateUsersList(array $users): string
    {
        return implode('', array_map(function($user) {
            return "<li>{$user['name']}: {$user['messages']} messages</li>";
        }, $users));
    }
} 