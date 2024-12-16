<?php

namespace HCH\ChatBotBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.conversations', 'c')
            ->groupBy('u.id')
            ->having('COUNT(c.id) > 0')
            ->getQuery()
            ->getResult();
    }
} 