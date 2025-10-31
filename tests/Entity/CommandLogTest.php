<?php

namespace TelegramBotBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\CommandLog;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(CommandLog::class)]
final class CommandLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new CommandLog();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'command' => ['command', '/start'];
        yield 'userId' => ['userId', 123456];
        yield 'username' => ['username', 'testuser'];
        yield 'chatId' => ['chatId', 789012];
        yield 'chatType' => ['chatType', 'private'];
        yield 'args' => ['args', ['param1' => 'value1', 'param2' => 'value2']];
    }

    public function testEntityCreation(): void
    {
        $log = new CommandLog();
        $this->assertInstanceOf(CommandLog::class, $log);
    }

    public function testDefaultValues(): void
    {
        $log = new CommandLog();
        $this->assertEquals(0, $log->getId());  // id 字段默认值为 0
        $this->assertEquals('', $log->getCommand());
        $this->assertFalse($log->isSystem());
        $this->assertNull($log->getArgs());
        $this->assertNull($log->getUserId());
        $this->assertNull($log->getUsername());
        $this->assertNull($log->getChatId());
        $this->assertNull($log->getChatType());
    }

    public function testToString(): void
    {
        $log = new CommandLog();
        $log->setCommand('/test');

        $this->assertStringContainsString('CommandLog', $log->__toString());
        $this->assertStringContainsString('/test', $log->__toString());
    }
}
