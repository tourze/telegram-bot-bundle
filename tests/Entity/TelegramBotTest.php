<?php

namespace TelegramBotBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\TelegramBot;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBot::class)]
final class TelegramBotTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TelegramBot();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试机器人'];
        yield 'username' => ['username', 'test_bot'];
        yield 'token' => ['token', '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'];
        yield 'webhookUrl' => ['webhookUrl', 'https://example.com/webhook'];
        yield 'description' => ['description', '这是一个测试机器人'];
        yield 'valid' => ['valid', true];
    }

    public function testEntityCreation(): void
    {
        $bot = new TelegramBot();
        $this->assertInstanceOf(TelegramBot::class, $bot);
        $this->assertNull($bot->getId());
    }
}
