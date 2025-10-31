<?php

namespace TelegramBotBundle\Tests\Entity\Embeddable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;

/**
 * @internal
 */
#[CoversClass(TelegramMessage::class)]
final class TelegramMessageTest extends TestCase
{
    private TelegramMessage $message;

    protected function setUp(): void
    {
        $this->message = new TelegramMessage();
    }

    public function testGetterAndSetter(): void
    {
        // 测试MessageId设置和获取
        $this->message->setMessageId(123);
        $this->assertEquals(123, $this->message->getMessageId());

        // 测试Date设置和获取
        $this->message->setDate(1609459200);
        $this->assertEquals(1609459200, $this->message->getDate());

        // 测试Text设置和获取
        $this->message->setText('Hello, world!');
        $this->assertEquals('Hello, world!', $this->message->getText());

        // 测试From设置和获取
        $user = new TelegramUser();
        $user->setId(456);
        $user->setUsername('test_user');
        $this->message->setFrom($user);
        $this->assertSame($user, $this->message->getFrom());
        $fromUser = $this->message->getFrom();
        $this->assertNotNull($fromUser);
        $this->assertEquals(456, $fromUser->getId());
        $this->assertEquals('test_user', $fromUser->getUsername());

        // 测试Chat设置和获取
        $chat = new TelegramChat();
        $chat->setId(789);
        $chat->setType('private');
        $this->message->setChat($chat);
        $this->assertSame($chat, $this->message->getChat());
        $messageChat = $this->message->getChat();
        $this->assertNotNull($messageChat);
        $this->assertEquals(789, $messageChat->getId());
        $this->assertEquals('private', $messageChat->getType());
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
