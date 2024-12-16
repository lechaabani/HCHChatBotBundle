<?php

namespace HCH\ChatBotBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use HCH\ChatBotBundle\Entity\Conversation;

class ChatBotCleanupCommand extends Command
{
    protected static $defaultName = 'hch:HCHChatBotBundle:cleanup';
    protected static $defaultDescription = 'Nettoie les anciennes conversations';

    public function __construct(
        private EntityManagerInterface $em,
        private int $daysToKeep = 30
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $date = new \DateTime("-{$this->daysToKeep} days");
        
        $qb = $this->em->createQueryBuilder();
        $query = $qb->delete(Conversation::class, 'c')
            ->where('c.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery();

        $result = $query->execute();

        $io->success(sprintf(
            'Nettoyage terminé. %d conversations supprimées.',
            $result
        ));

        return Command::SUCCESS;
    }
} 