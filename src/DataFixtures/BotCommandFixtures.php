<?php

namespace TelegramBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram机器人命令数据填充
 */
class BotCommandFixtures extends Fixture implements DependentFixtureInterface
{
    // 定义引用常量
    public const START_COMMAND_REFERENCE = 'command-start';
    public const HELP_COMMAND_REFERENCE = 'command-help';

    public function load(ObjectManager $manager): void
    {
        // 获取机器人引用
        $demoBot = $this->getReference(TelegramBotFixtures::DEMO_BOT_REFERENCE, TelegramBot::class);
        $testBot = $this->getReference(TelegramBotFixtures::TEST_BOT_REFERENCE, TelegramBot::class);

        // 为演示机器人创建start命令
        $startCommand = new BotCommand();
        $startCommand->setBot($demoBot);
        $startCommand->setCommand('start');
        $startCommand->setHandler('TelegramBotBundle\Handler\StartCommandHandler');
        $startCommand->setDescription('开始使用机器人');
        $startCommand->setValid(true);

        $manager->persist($startCommand);

        // 为演示机器人创建help命令
        $helpCommand = new BotCommand();
        $helpCommand->setBot($demoBot);
        $helpCommand->setCommand('help');
        $helpCommand->setHandler('TelegramBotBundle\Handler\HelpCommandHandler');
        $helpCommand->setDescription('显示帮助信息');
        $helpCommand->setValid(true);

        $manager->persist($helpCommand);

        // 为测试机器人创建test命令
        $testCommand = new BotCommand();
        $testCommand->setBot($testBot);
        $testCommand->setCommand('test');
        $testCommand->setHandler('TelegramBotBundle\Handler\TestCommandHandler');
        $testCommand->setDescription('测试命令');
        $testCommand->setValid(true);

        $manager->persist($testCommand);

        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::START_COMMAND_REFERENCE, $startCommand);
        $this->addReference(self::HELP_COMMAND_REFERENCE, $helpCommand);
    }

    public function getDependencies(): array
    {
        return [
            TelegramBotFixtures::class,
        ];
    }
}
