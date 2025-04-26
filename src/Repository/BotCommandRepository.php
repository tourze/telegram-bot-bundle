<?php

namespace TelegramBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * @extends ServiceEntityRepository<BotCommand>
 *
 * @method BotCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method BotCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method BotCommand[]    findAll()
 * @method BotCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BotCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BotCommand::class);
    }

    /**
     * 获取机器人的所有有效命令
     *
     * @return BotCommand[]
     */
    public function getValidCommands(TelegramBot $bot): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.bot = :bot')
            ->andWhere('c.valid = true')
            ->setParameter('bot', $bot)
            ->orderBy('c.command', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 获取机器人的指定命令
     */
    public function findCommand(TelegramBot $bot, string $command): ?BotCommand
    {
        return $this->findOneBy([
            'bot' => $bot,
            'command' => $command,
            'valid' => true,
        ]);
    }
}
