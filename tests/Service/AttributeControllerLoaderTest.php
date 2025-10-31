<?php

namespace TelegramBotBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use TelegramBotBundle\Service\AttributeControllerLoader;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 此测试不需要特殊的设置逻辑
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $result = $loader->load('dummy-resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupportsReturnsFalse(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);

        $this->assertFalse($loader->supports('any-resource'));
        $this->assertFalse($loader->supports('any-resource', 'any-type'));
    }

    public function testAutoloadReturnsRouteCollectionWithWebhookRoutes(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $collection = $loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $collection);

        // 验证路由集合不为空（应该包含WebhookController的路由）
        $this->assertGreaterThan(0, $collection->count());

        // 验证包含预期的路由名称
        $routeNames = array_keys($collection->all());
        $this->assertContains('telegram_bot_webhook', $routeNames);
    }

    public function testLoadUsesAutoloadMethod(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);

        $loadResult = $loader->load('test-resource');
        $autoloadResult = $loader->autoload();

        // 验证load方法实际使用了autoload方法
        $this->assertEquals($autoloadResult->count(), $loadResult->count());
        $this->assertEquals(
            array_keys($autoloadResult->all()),
            array_keys($loadResult->all())
        );
    }
}
