<?php

namespace TelegramBotBundle\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Handler\CommandHandlerInterface;
use TelegramBotBundle\Repository\BotCommandRepository;
use TelegramBotBundle\Service\CommandParserService;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;

class CommandParserServiceTest extends TestCase
{
    private CommandParserService $commandParserService;
    private MockObject|BotCommandRepository $commandRepository;
    private MockObject|DoctrineService $doctrineService;
    private MockObject|LoggerInterface $logger;
    private MockObject|ContainerInterface $container;
    private TelegramBot $bot;
    private TelegramMessage $message;
    private MockObject|CommandHandlerInterface $commandHandler;

    protected function setUp(): void
    {
        $this->commandRepository = $this->createMock(BotCommandRepository::class);
        $this->doctrineService = $this->createMock(DoctrineService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);

        $this->commandParserService = new CommandParserService(
            $this->commandRepository,
            $this->doctrineService,
            $this->logger,
            $this->container
        );

        $this->bot = new TelegramBot();

        $this->message = new TelegramMessage();
        $this->message->setChat(new TelegramChat());
        $this->message->setFrom(new TelegramUser());

        $this->commandHandler = $this->createMock(CommandHandlerInterface::class);
    }

    public function testParseAndDispatch_withValidCommand(): void
    {
        $this->message->setText('/test');

        $this->commandRepository->expects($this->once())
            ->method('findCommand')
            ->with($this->bot, 'test')
            ->willReturn(null);

        // 在模拟此测试时，不会执行后续命令处理逻辑
        $this->logger->expects($this->once())
            ->method('info')
            ->with('未知的命令');

        $this->commandParserService->parseAndDispatch($this->bot, $this->message);
    }

    public function testParseAndDispatch_withArgumentsInCommand(): void
    {
        $this->message->setText('/test arg1 arg2 "arg with spaces"');

        $this->commandRepository->expects($this->once())
            ->method('findCommand')
            ->with($this->bot, 'test')
            ->willReturn(null);

        // 在模拟此测试时，不会执行后续命令处理逻辑
        $this->logger->expects($this->once())
            ->method('info')
            ->with('未知的命令');

        $this->commandParserService->parseAndDispatch($this->bot, $this->message);
    }

    public function testParseAndDispatch_withEmptyMessage(): void
    {
        $this->message->setText('');

        // 当消息为空时，不应该尝试查找命令
        $this->commandRepository->expects($this->never())
            ->method('findCommand');

        $this->commandParserService->parseAndDispatch($this->bot, $this->message);
    }

    public function testParseAndDispatch_withNonCommandMessage(): void
    {
        $this->message->setText('This is not a command');

        // 当消息不是命令时，不应该尝试查找命令
        $this->commandRepository->expects($this->never())
            ->method('findCommand');

        $this->commandParserService->parseAndDispatch($this->bot, $this->message);
    }

    public function testParseAndDispatch_withSystemCommand(): void
    {
        $this->message->setText('/info');

        $this->container->expects($this->once())
            ->method('get')
            ->willReturn($this->commandHandler);

        $this->commandHandler->expects($this->once())
            ->method('handle')
            ->with($this->bot, 'info', [], $this->message);

        $this->commandParserService->parseAndDispatch($this->bot, $this->message);
    }
}
