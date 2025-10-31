<?php

namespace TelegramBotBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Service\TelegramBotService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBotService::class)]
#[RunTestsInSeparateProcesses]
final class TelegramBotServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试不需要手动设置
    }

    public function testServiceCanBeCreated(): void
    {
        // 验证服务可以从容器获取
        $service = self::getService(TelegramBotService::class);
        $this->assertInstanceOf(TelegramBotService::class, $service);
    }

    public function testBotEntityCreation(): void
    {
        // 创建一个测试 bot
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setUsername('test_bot');
        $bot->setToken('test_token_123456789');

        // 验证 bot 属性设置正确
        $this->assertSame('Test Bot', $bot->getName());
        $this->assertSame('test_bot', $bot->getUsername());
        $this->assertSame('test_token_123456789', $bot->getToken());
        $this->assertNull($bot->getId()); // 新创建的实体 ID 为 null
    }

    public function testServiceImplementsExpectedInterface(): void
    {
        // 获取服务实例
        $service = self::getService(TelegramBotService::class);

        // 验证服务具有预期的方法（这里只测试服务能够正确注册和获取）
        $this->assertTrue(method_exists($service, 'setWebhook'));
        $this->assertTrue(method_exists($service, 'sendMessage'));
        $this->assertTrue(method_exists($service, 'makeHttpRequest'));
    }

    public function testMakeHttpRequest(): void
    {
        $service = self::getService(TelegramBotService::class);

        // 测试方法存在且可调用
        $this->assertTrue(method_exists($service, 'makeHttpRequest'));

        // 由于需要真实的HTTP请求，这里只测试方法签名
        $reflection = new \ReflectionMethod($service, 'makeHttpRequest');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(3, $reflection->getNumberOfParameters());
    }

    public function testSendMessage(): void
    {
        $service = self::getService(TelegramBotService::class);
        $bot = new TelegramBot();
        $bot->setToken('test-token');

        // 测试方法存在且可调用
        $this->assertTrue(method_exists($service, 'sendMessage'));

        // 由于需要真实的HTTP请求，这里只测试方法签名
        $reflection = new \ReflectionMethod($service, 'sendMessage');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(3, $reflection->getNumberOfParameters());
    }
}
