<?php

namespace TelegramBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * @method TelegramBot|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramBot|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramBot[]    findAll()
 * @method TelegramBot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramBotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramBot::class);
    }
}
