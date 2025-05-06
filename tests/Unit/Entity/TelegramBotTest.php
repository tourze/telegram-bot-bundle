<?php

namespace TelegramBotBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\TelegramBot;

class TelegramBotTest extends TestCase
{
    private TelegramBot $bot;

    protected function setUp(): void
    {
        $this->bot = new TelegramBot();
    }

    public function testGetterAndSetter(): void
    {
        // ID不能直接设置，所以跳过测试

        // 测试Token设置和获取
        $this->bot->setToken('test_token');
        $this->assertEquals('test_token', $this->bot->getToken());

        // 测试WebhookUrl设置和获取
        $this->bot->setWebhookUrl('https://example.com/webhook');
        $this->assertEquals('https://example.com/webhook', $this->bot->getWebhookUrl());

        // 测试Valid设置和获取
        $this->bot->setValid(true);
        $this->assertTrue($this->bot->isValid());

        // 测试Name设置和获取
        $this->bot->setName('Test Bot');
        $this->assertEquals('Test Bot', $this->bot->getName());

        // 测试Username设置和获取
        $this->bot->setUsername('test_bot');
        $this->assertEquals('test_bot', $this->bot->getUsername());

        // 测试描述设置和获取
        $this->bot->setDescription('This is a test bot');
        $this->assertEquals('This is a test bot', $this->bot->getDescription());
    }

    public function testDefaultValues(): void
    {
        // 测试默认值
        $bot = new TelegramBot();
        $this->assertNull($bot->getId());
        $this->assertEquals('', $bot->getName());
        $this->assertEquals('', $bot->getUsername());
        $this->assertEquals('', $bot->getToken());
        $this->assertNull($bot->getWebhookUrl());
        $this->assertNull($bot->getDescription());
        $this->assertFalse($bot->isValid());
    }

    public function testToArray(): void
    {
        $this->bot->setName('Test Bot');
        $this->bot->setUsername('test_bot');
        $this->bot->setToken('test_token');

        $array = $this->bot->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('token', $array);
        $this->assertEquals('Test Bot', $array['name']);
        $this->assertEquals('test_bot', $array['username']);
        $this->assertEquals('test_token', $array['token']);
    }

    public function testToString(): void
    {
        $this->bot->setName('Test Bot');

        // 在ID为null的情况下应该返回空字符串
        $this->assertEquals('', $this->bot->__toString());

        // 注意：无法测试非空ID的情况，因为ID是由系统生成的
    }
}
