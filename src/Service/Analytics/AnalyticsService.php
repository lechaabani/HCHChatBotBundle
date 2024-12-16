<?php

namespace HCH\ChatBotBundle\Service\Analytics;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use HCH\ChatBotBundle\Entity\Conversation;
use HCH\ChatBotBundle\Entity\Message;

class AnalyticsService
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function trackConversation(string $message, string $response, array $metadata = []): void
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session->getId();

        $conversation = $this->em->getRepository(Conversation::class)
            ->findOneBy(['sessionId' => $sessionId]) ?? new Conversation();

        if (!$conversation->getId()) {
            $conversation->setSessionId($sessionId);
            if ($session->has('user_id')) {
                $conversation->setUserId($session->get('user_id'));
            }
        }

        $userMessage = new Message();
        $userMessage->setContent($message)
            ->setRole('user')
            ->setMetadata($metadata);
        
        $botMessage = new Message();
        $botMessage->setContent($response)
            ->setRole('assistant')
            ->setMetadata($metadata);

        $conversation->addMessage($userMessage)
            ->addMessage($botMessage);

        $this->em->persist($conversation);
        $this->em->flush();
    }

    public function getStatistics(): array
    {
        $qb = $this->em->createQueryBuilder();
        
        $totalConversations = $qb->select('COUNT(c.id)')
            ->from(Conversation::class, 'c')
            ->getQuery()
            ->getSingleScalarResult();

        $totalMessages = $qb->select('COUNT(m.id)')
            ->from(Message::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $averageMessagesPerConversation = $totalConversations > 0 
            ? $totalMessages / $totalConversations 
            : 0;

        $topUsers = $qb->select('c.userId, COUNT(c.id) as conv_count')
            ->from(Conversation::class, 'c')
            ->where('c.userId IS NOT NULL')
            ->groupBy('c.userId')
            ->orderBy('conv_count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return [
            'total_conversations' => $totalConversations,
            'total_messages' => $totalMessages,
            'avg_messages_per_conversation' => $averageMessagesPerConversation,
            'top_users' => $topUsers,
            'last_24h' => $this->getLast24HoursStats(),
        ];
    }

    private function getLast24HoursStats(): array
    {
        $qb = $this->em->createQueryBuilder();
        $yesterday = new \DateTime('-24 hours');

        $conversations = $qb->select('COUNT(c.id)')
            ->from(Conversation::class, 'c')
            ->where('c.createdAt >= :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();

        $messages = $qb->select('COUNT(m.id)')
            ->from(Message::class, 'm')
            ->join('m.conversation', 'c')
            ->where('c.createdAt >= :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'conversations' => $conversations,
            'messages' => $messages,
        ];
    }
} 