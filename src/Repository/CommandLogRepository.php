<?php

namespace TelegramBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TelegramBotBundle\Entity\CommandLog;

/**
 * @extends ServiceEntityRepository<CommandLog>
 *
 * @method CommandLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandLog[]    findAll()
 * @method CommandLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandLog::class);
    }
}
