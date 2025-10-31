<?php

namespace TelegramBotBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\DependencyInjection\TelegramBotExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBotExtension::class)]
final class TelegramBotExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private TelegramBotExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new TelegramBotExtension();
    }

    public function testLoad(): void
    {
        // 验证扩展已正确加载
        $this->assertInstanceOf(TelegramBotExtension::class, $this->extension);
    }

    public function testGetAlias(): void
    {
        // 验证扩展别名
        $this->assertEquals('telegram_bot', $this->extension->getAlias());
    }
}
