<?php

namespace TelegramBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram机器人数据填充
 */
class TelegramBotFixtures extends Fixture
{
    // 定义引用常量
    public const DEMO_BOT_REFERENCE = 'telegram-bot-demo';
    public const TEST_BOT_REFERENCE = 'telegram-bot-test';

    public function load(ObjectManager $manager): void
    {
        // 创建演示机器人
        $demoBot = new TelegramBot();
        $demoBot->setName('演示机器人');
        $demoBot->setUsername('demo_bot');
        $demoBot->setToken('demo_bot_token_12345');
        $demoBot->setWebhookUrl('https://example.com/webhook/demo');
        $demoBot->setDescription('用于演示的Telegram机器人');
        $demoBot->setValid(true);

        $manager->persist($demoBot);

        // 创建测试机器人
        $testBot = new TelegramBot();
        $testBot->setName('测试机器人');
        $testBot->setUsername('test_bot');
        $testBot->setToken('test_bot_token_67890');
        $testBot->setWebhookUrl('https://example.com/webhook/test');
        $testBot->setDescription('用于测试的Telegram机器人');
        $testBot->setValid(true);

        $manager->persist($testBot);

        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::DEMO_BOT_REFERENCE, $demoBot);
        $this->addReference(self::TEST_BOT_REFERENCE, $testBot);
    }
}
