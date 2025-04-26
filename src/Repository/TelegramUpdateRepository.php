<?php

namespace TelegramBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;

/**
 * @extends ServiceEntityRepository<TelegramUpdate>
 *
 * @method TelegramUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramUpdate[]    findAll()
 * @method TelegramUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramUpdate::class);
    }

    /**
     * 根据机器人和更新ID查找消息
     */
    public function findByBotAndUpdateId(TelegramBot $bot, string $updateId): ?TelegramUpdate
    {
        return $this->findOneBy(['bot' => $bot, 'updateId' => $updateId]);
    }

    /**
     * 分页获取机器人的消息列表
     *
     * @return TelegramUpdate[]
     */
    public function getListByBot(TelegramBot $bot, int $page = 1, int $limit = 20): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.bot = :bot')
            ->setParameter('bot', $bot)
            ->orderBy('t.updateId', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * 获取机器人的消息总数
     */
    public function getTotalByBot(TelegramBot $bot): int
    {
        return $this->count(['bot' => $bot]);
    }

    /**
     * 获取机器人的最后一条消息
     */
    public function getLastUpdateByBot(TelegramBot $bot): ?TelegramUpdate
    {
        return $this->createQueryBuilder('t')
            ->where('t.bot = :bot')
            ->setParameter('bot', $bot)
            ->orderBy('t.updateId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
