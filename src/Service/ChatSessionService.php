<?php

namespace HCH\ChatBotBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HCH\ChatBotBundle\Entity\Conversation;
use HCH\ChatBotBundle\Entity\Message;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatSessionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private array $config
    ) {}

    public function startNewSession(): Conversation
    {
        $session = $this->requestStack->getSession();
        $conversation = new Conversation();
        $conversation->setSessionId($session->getId());
        
        if ($session->has('user_id')) {
            $conversation->setUserId($session->get('user_id'));
        }

        $this->em->persist($conversation);
        $this->em->flush();

        return $conversation;
    }

    public function addMessage(string $content, string $role, Conversation $conversation, array $metadata = []): Message
    {
        $message = new Message();
        $message->setContent($content)
            ->setRole($role)
            ->setMetadata($metadata)
            ->setConversation($conversation);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function getCurrentConversation(): ?Conversation
    {
        $session = $this->requestStack->getSession();
        
        $conversation = $this->em->getRepository(Conversation::class)
            ->findOneBy(['sessionId' => $session->getId()], ['createdAt' => 'DESC']);

        if (!$conversation) {
            $conversation = $this->startNewSession();
        }

        return $conversation;
    }

    public function getHistory(int $limit = 10): array
    {
        $conversation = $this->getCurrentConversation();
        
        return $this->em->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->where('m.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 