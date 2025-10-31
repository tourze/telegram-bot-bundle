<?php

namespace TelegramBotBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramUpdateEvent::class)]
final class TelegramUpdateEventTest extends AbstractEventTestCase
{
    protected function onSetUp(): void
    {
        // 此测试不需要特殊的设置逻辑
    }

    public function testEventCreationAndGetters(): void
    {
        $bot = new TelegramBot();
        $update = new TelegramUpdate();
        $rawData = ['update_id' => 123, 'message' => ['text' => 'Hello']];
        $update->setRawData($rawData);

        $event = new TelegramUpdateEvent($bot, $update);

        $this->assertSame($bot, $event->getBot());
        $this->assertSame($update, $event->getUpdate());
        $this->assertSame(['text' => 'Hello'], $event->getMessage());
    }
}
