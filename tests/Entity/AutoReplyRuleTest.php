<?php

namespace TelegramBotBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Entity\AutoReplyRule;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AutoReplyRule::class)]
final class AutoReplyRuleTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AutoReplyRule();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试规则'];
        yield 'keyword' => ['keyword', '/start'];
        yield 'replyContent' => ['replyContent', '欢迎使用'];
        yield 'exactMatch' => ['exactMatch', true];
        yield 'priority' => ['priority', 100];
        yield 'valid' => ['valid', false];
    }

    public function testEntityCreation(): void
    {
        $rule = new AutoReplyRule();
        $this->assertInstanceOf(AutoReplyRule::class, $rule);
        $this->assertSame(0, $rule->getId()); // AutoReplyRule 使用自增ID，默认值为0
    }
}
