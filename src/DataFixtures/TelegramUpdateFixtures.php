<?php

namespace TelegramBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;

/**
 * Telegram更新消息数据填充
 */
class TelegramUpdateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 获取机器人引用
        $demoBot = $this->getReference(TelegramBotFixtures::DEMO_BOT_REFERENCE, TelegramBot::class);

        // 创建文本消息更新
        $textUpdate = new TelegramUpdate();
        $textUpdate->setBot($demoBot);
        $textUpdate->setUpdateId('123456789');

        // 创建消息发送者
        $fromUser = new TelegramUser();
        $fromUser->setId(123456789);
        $fromUser->setUsername('demo_user');
        $fromUser->setFirstName('Demo');
        $fromUser->setIsBot(false);

        // 创建聊天信息
        $chat = new TelegramChat();
        $chat->setId(123456789);
        $chat->setType('private');
        $chat->setUsername('demo_user');
        $chat->setFirstName('Demo');

        // 创建消息实体
        $textMessage = new TelegramMessage();
        $textMessage->setMessageId(1001);
        $textMessage->setFrom($fromUser);
        $textMessage->setChat($chat);
        $textMessage->setText('你好，机器人！');
        $textMessage->setDate(strtotime('2023-01-01 10:00:00'));

        $textUpdate->setMessage($textMessage);

        // 原始数据
        $textUpdate->setRawData([
            'update_id' => 123456789,
            'message' => [
                'message_id' => 1001,
                'from' => [
                    'id' => 123456789,
                    'username' => 'demo_user',
                ],
                'chat' => [
                    'id' => 123456789,
                    'type' => 'private',
                ],
                'text' => '你好，机器人！',
                'date' => 1672567200, // 2023-01-01 10:00:00
            ],
        ]);

        $textUpdate->setCreateTime(new \DateTime('2023-01-01 10:00:00'));

        $manager->persist($textUpdate);

        // 创建命令消息更新
        $commandUpdate = new TelegramUpdate();
        $commandUpdate->setBot($demoBot);
        $commandUpdate->setUpdateId('123456790');

        // 创建消息发送者
        $commandFromUser = new TelegramUser();
        $commandFromUser->setId(123456789);
        $commandFromUser->setUsername('demo_user');
        $commandFromUser->setFirstName('Demo');
        $commandFromUser->setIsBot(false);

        // 创建聊天信息
        $commandChat = new TelegramChat();
        $commandChat->setId(123456789);
        $commandChat->setType('private');
        $commandChat->setUsername('demo_user');
        $commandChat->setFirstName('Demo');

        // 创建消息实体
        $commandMessage = new TelegramMessage();
        $commandMessage->setMessageId(1002);
        $commandMessage->setFrom($commandFromUser);
        $commandMessage->setChat($commandChat);
        $commandMessage->setText('/start');
        $commandMessage->setDate(strtotime('2023-01-01 10:01:00'));

        $commandUpdate->setMessage($commandMessage);

        // 原始数据
        $commandUpdate->setRawData([
            'update_id' => 123456790,
            'message' => [
                'message_id' => 1002,
                'from' => [
                    'id' => 123456789,
                    'username' => 'demo_user',
                ],
                'chat' => [
                    'id' => 123456789,
                    'type' => 'private',
                ],
                'text' => '/start',
                'date' => 1672567260, // 2023-01-01 10:01:00
            ],
        ]);

        $commandUpdate->setCreateTime(new \DateTime('2023-01-01 10:01:00'));

        $manager->persist($commandUpdate);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TelegramBotFixtures::class,
        ];
    }
}
