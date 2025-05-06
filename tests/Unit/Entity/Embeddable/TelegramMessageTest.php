<?php

namespace TelegramBotBundle\Tests\Unit\Entity\Embeddable;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;

class TelegramMessageTest extends TestCase
{
    private TelegramMessage $message;

    protected function setUp(): void
    {
        $this->message = new TelegramMessage();
    }

    public function testGetterAndSetter(): void
    {
        // 测试MessageId设置和获取
        $this->message->setMessageId('123');
        $this->assertEquals('123', $this->message->getMessageId());

        // 测试Date设置和获取
        $this->message->setDate(1609459200);
        $this->assertEquals(1609459200, $this->message->getDate());

        // 测试Text设置和获取
        $this->message->setText('Hello, world!');
        $this->assertEquals('Hello, world!', $this->message->getText());

        // 测试From设置和获取
        $user = new TelegramUser();
        $user->setId('456');
        $user->setUsername('test_user');
        $this->message->setFrom($user);
        $this->assertSame($user, $this->message->getFrom());
        $this->assertEquals('456', $this->message->getFrom()->getId());
        $this->assertEquals('test_user', $this->message->getFrom()->getUsername());

        // 测试Chat设置和获取
        $chat = new TelegramChat();
        $chat->setId('789');
        $chat->setType('private');
        $this->message->setChat($chat);
        $this->assertSame($chat, $this->message->getChat());
        $this->assertEquals('789', $this->message->getChat()->getId());
        $this->assertEquals('private', $this->message->getChat()->getType());
    }

    public function testDefaultValues(): void
    {
        // 测试默认值
        $message = new TelegramMessage();
        $this->assertNull($message->getMessageId());
        $this->assertNull($message->getDate());
        $this->assertNull($message->getText());
        $this->assertNull($message->getFrom());
        $this->assertNull($message->getChat());
    }
}
