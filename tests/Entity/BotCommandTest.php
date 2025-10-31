<?php

namespace TelegramBotBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\BotCommand;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(BotCommand::class)]
final class BotCommandTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new BotCommand();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'command' => ['command', '/help'];
        yield 'handler' => ['handler', 'App\Handler\HelpHandler'];
        yield 'description' => ['description', '显示帮助信息'];
        yield 'valid' => ['valid', true];
    }

    public function testEntityCreation(): void
    {
        $command = new BotCommand();
        $this->assertInstanceOf(BotCommand::class, $command);
        $this->assertNull($command->getId());
    }
}
