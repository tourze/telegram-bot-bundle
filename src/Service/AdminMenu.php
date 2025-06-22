<?php

namespace TelegramBotBundle\Service;

use Knp\Menu\ItemInterface;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\CommandLog;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * Telegram机器人菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    )
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('TG机器人')) {
            $item->addChild('TG机器人');
        }

        $telegramMenu = $item->getChild('TG机器人');

        // 机器人管理菜单
        $telegramMenu->addChild('机器人管理')
            ->setUri($this->linkGenerator->getCurdListPage(TelegramBot::class))
            ->setAttribute('icon', 'fas fa-robot');

        // 机器人命令菜单
        $telegramMenu->addChild('命令管理')
            ->setUri($this->linkGenerator->getCurdListPage(BotCommand::class))
            ->setAttribute('icon', 'fas fa-terminal');

        // 自动回复规则菜单
        $telegramMenu->addChild('自动回复规则')
            ->setUri($this->linkGenerator->getCurdListPage(AutoReplyRule::class))
            ->setAttribute('icon', 'fas fa-comment-dots');

        // 命令日志菜单
        $telegramMenu->addChild('命令日志')
            ->setUri($this->linkGenerator->getCurdListPage(CommandLog::class))
            ->setAttribute('icon', 'fas fa-history');

        // 消息更新菜单
        $telegramMenu->addChild('消息记录')
            ->setUri($this->linkGenerator->getCurdListPage(TelegramUpdate::class))
            ->setAttribute('icon', 'fas fa-envelope');
    }
}
