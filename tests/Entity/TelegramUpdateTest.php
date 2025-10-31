<?php

namespace TelegramBotBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\TelegramUpdate;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramUpdate::class)]
final class TelegramUpdateTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TelegramUpdate();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'updateId' => ['updateId', '123456'];
        yield 'rawData' => ['rawData', ['update_id' => 123456, 'message' => ['text' => 'Hello']]];
    }

    public function testEntityCreation(): void
    {
        $update = new TelegramUpdate();
        $this->assertInstanceOf(TelegramUpdate::class, $update);
        $this->assertNull($update->getId());
    }

    public function testRawDataAcceptsEmptyArray(): void
    {
        $update = new TelegramUpdate();
        $update->setRawData([]);
        $this->assertSame([], $update->getRawData());
    }

    public function testRawDataAcceptsComplexStructure(): void
    {
        $update = new TelegramUpdate();
        $complexData = [
            'update_id' => 123456,
            'message' => [
                'message_id' => 789,
                'from' => [
                    'id' => 123456789,
                    'first_name' => 'John',
                    'username' => 'john_doe',
                ],
                'chat' => [
                    'id' => 987654321,
                    'first_name' => 'John',
                    'username' => 'john_doe',
                    'type' => 'private',
                ],
                'date' => 1640995200,
                'text' => 'Hello World',
            ],
        ];
        $update->setRawData($complexData);
        $this->assertSame($complexData, $update->getRawData());
    }
}
