<?php

namespace TelegramBotBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Event\TelegramCommandEvent;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramCommandEvent::class)]
final class TelegramCommandEventTest extends AbstractEventTestCase
{
    protected function onSetUp(): void
    {
        // 此测试不需要特殊的设置逻辑
    }

    public function testEventCreationAndGetters(): void
    {
        $bot = new TelegramBot();
        $message = new TelegramMessage();
        $command = '/start';
        $args = ['arg1', 'arg2'];

        $event = new TelegramCommandEvent($bot, $command, $args, $message);

        $this->assertSame($bot, $event->getBot());
        $this->assertSame($command, $event->getCommand());
        $this->assertSame($args, $event->getArgs());
        $this->assertSame($message, $event->getMessage());
    }
}
