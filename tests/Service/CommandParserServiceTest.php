<?php

namespace TelegramBotBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Service\CommandParserService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(CommandParserService::class)]
#[RunTestsInSeparateProcesses]
final class CommandParserServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试不需要手动设置
    }

    public function testServiceCanBeCreated(): void
    {
        // 验证服务可以从容器获取
        $service = self::getService(CommandParserService::class);
        $this->assertInstanceOf(CommandParserService::class, $service);
    }

    public function testServiceImplementsExpectedInterface(): void
    {
        // 获取服务实例
        $service = self::getService(CommandParserService::class);

        // 验证服务具有预期的方法（这里只测试服务能够正确注册和获取）
        $this->assertTrue(method_exists($service, 'parseAndDispatch'));
    }

    public function testParseAndDispatch(): void
    {
        $service = self::getService(CommandParserService::class);

        // 创建并持久化一个测试用的机器人
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        $bot->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($bot);
        $entityManager->flush();

        $message = new TelegramMessage();
        $message->setText('/info');

        // 测试方法不抛出异常
        $this->expectNotToPerformAssertions();
        $service->parseAndDispatch($bot, $message);
    }
}
