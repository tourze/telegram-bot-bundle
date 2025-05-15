<?php

namespace TelegramBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TelegramBotBundle\Entity\CommandLog;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram命令日志数据填充
 */
class CommandLogFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 获取机器人引用
        $demoBot = $this->getReference(TelegramBotFixtures::DEMO_BOT_REFERENCE, TelegramBot::class);

        // 创建start命令日志
        $startLog = new CommandLog();
        $startLog->setBot($demoBot);
        $startLog->setCommand('start');
        $startLog->setArgs(['param' => 'value']);
        $startLog->setIsSystem(false);
        $startLog->setUserId(123456789);
        $startLog->setUsername('demo_user');
        $startLog->setChatId(123456789);
        $startLog->setChatType('private');
        $startLog->setCreateTime(new \DateTime('2023-01-01 10:00:00'));

        $manager->persist($startLog);

        // 创建help命令日志
        $helpLog = new CommandLog();
        $helpLog->setBot($demoBot);
        $helpLog->setCommand('help');
        $helpLog->setArgs(null);
        $helpLog->setIsSystem(false);
        $helpLog->setUserId(123456789);
        $helpLog->setUsername('demo_user');
        $helpLog->setChatId(123456789);
        $helpLog->setChatType('private');
        $helpLog->setCreateTime(new \DateTime('2023-01-01 10:05:00'));

        $manager->persist($helpLog);

        // 创建系统命令日志
        $systemLog = new CommandLog();
        $systemLog->setBot($demoBot);
        $systemLog->setCommand('setCommands');
        $systemLog->setArgs(['commands' => [['command' => 'start', 'description' => '开始使用机器人']]]);
        $systemLog->setIsSystem(true);
        $systemLog->setUserId(null);
        $systemLog->setUsername(null);
        $systemLog->setChatId(null);
        $systemLog->setChatType(null);
        $systemLog->setCreateTime(new \DateTime('2023-01-01 09:00:00'));

        $manager->persist($systemLog);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TelegramBotFixtures::class,
        ];
    }
}
