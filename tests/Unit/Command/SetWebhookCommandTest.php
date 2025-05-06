<?php

namespace TelegramBotBundle\Tests\Unit\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TelegramBotBundle\Command\SetWebhookCommand;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\TelegramBotRepository;
use TelegramBotBundle\Service\TelegramBotService;

class SetWebhookCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject|TelegramBotRepository $botRepository;
    private MockObject|TelegramBotService $botService;
    private MockObject|UrlGeneratorInterface $urlGenerator;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        $this->botRepository = $this->createMock(TelegramBotRepository::class);
        $this->botService = $this->createMock(TelegramBotService::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $command = new SetWebhookCommand(
            $this->botRepository,
            $this->botService,
            $this->urlGenerator
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find(SetWebhookCommand::NAME));

        $this->bot = new TelegramBot();
        // 不再设置ID，因为在TelegramBot实体中ID是由Doctrine自动生成的
    }

    public function testExecute_withValidParameters(): void
    {
        $this->botRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($this->bot);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('telegram_bot_webhook', ['id' => null])
            ->willReturn('/telegram/webhook/123');

        $this->botService->expects($this->once())
            ->method('setWebhook')
            ->with($this->bot, 'https://example.com/telegram/webhook/123')
            ->willReturn(true);

        $this->commandTester->execute([
            'bot-id' => '123',
            'base-url' => 'https://example.com',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Successfully', $output);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecute_withEmptyBaseUrl(): void
    {
        $this->commandTester->execute([
            'bot-id' => '123',
            'base-url' => '',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('基础URL不能为空', $output);
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecute_withNonExistentBot(): void
    {
        $this->botRepository->expects($this->once())
            ->method('find')
            ->with('nonexistent')
            ->willReturn(null);

        $this->commandTester->execute([
            'bot-id' => 'nonexistent',
            'base-url' => 'https://example.com',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Bot not found', $output);
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecute_whenServiceFailsToSetWebhook(): void
    {
        $this->botRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($this->bot);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('telegram_bot_webhook', ['id' => null])
            ->willReturn('/telegram/webhook/123');

        $this->botService->expects($this->once())
            ->method('setWebhook')
            ->with($this->bot, 'https://example.com/telegram/webhook/123')
            ->willReturn(false);

        $this->commandTester->execute([
            'bot-id' => '123',
            'base-url' => 'https://example.com',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Failed to set webhook URL', $output);
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecute_withTrailingSlashInBaseUrl(): void
    {
        $this->botRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($this->bot);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('telegram_bot_webhook', ['id' => null])
            ->willReturn('/telegram/webhook/123');

        // 确保尾部斜杠被正确处理
        $this->botService->expects($this->once())
            ->method('setWebhook')
            ->with($this->bot, 'https://example.com/telegram/webhook/123')
            ->willReturn(true);

        $this->commandTester->execute([
            'bot-id' => '123',
            'base-url' => 'https://example.com/',
        ]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
