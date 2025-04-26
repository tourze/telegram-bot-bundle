<?php

namespace TelegramBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TelegramBotBundle\Entity\AutoReplyRule;

/**
 * @method AutoReplyRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutoReplyRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutoReplyRule[]    findAll()
 * @method AutoReplyRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutoReplyRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutoReplyRule::class);
    }

    /**
     * 根据机器人ID和消息内容查找匹配的规则
     *
     * @return AutoReplyRule[]
     */
    public function findMatchingRules(string $botId, string $messageContent): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.bot = :botId')
            ->andWhere('r.valid = true')
            ->setParameter('botId', $botId)
            ->orderBy('r.priority', 'DESC')
            ->addOrderBy('r.id', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
