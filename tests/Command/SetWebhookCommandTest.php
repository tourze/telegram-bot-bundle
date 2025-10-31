<?php

namespace TelegramBotBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TelegramBotBundle\Command\SetWebhookCommand;
use TelegramBotBundle\Entity\TelegramBot;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SetWebhookCommand::class)]
#[RunTestsInSeparateProcesses]
final class SetWebhookCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置逻辑
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SetWebhookCommand::class);

        return new CommandTester($command);
    }

    public function testExecuteWithBotNotFoundReturnsFailure(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            'bot-id' => '999',
            'base-url' => 'https://example.com',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Bot not found', $commandTester->getDisplay());
    }

    public function testExecuteWithEmptyBaseUrlReturnsFailure(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            'bot-id' => '1',
            'base-url' => '',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('基础URL不能为空', $commandTester->getDisplay());
    }

    public function testExecuteWithValidBotAndUrlReturnsFailure(): void
    {
        // 创建测试用机器人
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        $bot->setWebhookUrl('');

        $em = self::getEntityManager();
        $em->persist($bot);
        $em->flush();

        $commandTester = $this->getCommandTester();

        // 执行命令，由于使用无效token，应该返回失败
        $exitCode = $commandTester->execute([
            'bot-id' => (string) $bot->getId(),
            'base-url' => 'https://example.com',
        ]);

        // 由于无效token，API调用应该失败
        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Failed to set webhook URL', $commandTester->getDisplay());
    }

    public function testExecuteWithServiceFailureReturnsFailure(): void
    {
        // 创建测试用机器人
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        $bot->setWebhookUrl('');

        $em = self::getEntityManager();
        $em->persist($bot);
        $em->flush();

        $commandTester = $this->getCommandTester();

        // 执行命令，由于使用无效token，应该返回失败
        $exitCode = $commandTester->execute([
            'bot-id' => (string) $bot->getId(),
            'base-url' => 'https://example.com',
        ]);

        // 由于无效token，API调用应该失败
        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Failed to set webhook URL', $commandTester->getDisplay());
    }

    public function testArgumentBotId(): void
    {
        $commandTester = $this->getCommandTester();

        // 测试 bot-id 参数为空字符串
        $exitCode = $commandTester->execute([
            'bot-id' => '',
            'base-url' => 'https://example.com',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Bot not found', $commandTester->getDisplay());
    }

    public function testArgumentBaseUrl(): void
    {
        $commandTester = $this->getCommandTester();

        // 测试 base-url 参数为空字符串
        $exitCode = $commandTester->execute([
            'bot-id' => '1',
            'base-url' => '',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('基础URL不能为空', $commandTester->getDisplay());
    }
}
