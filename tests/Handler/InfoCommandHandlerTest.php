<?php

namespace TelegramBotBundle\Tests\Handler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Handler\InfoCommandHandler;
use TelegramBotBundle\Repository\BotCommandRepository;
use TelegramBotBundle\Service\TelegramBotService;

/**
 * @internal
 */
#[CoversClass(InfoCommandHandler::class)]
final class InfoCommandHandlerTest extends TestCase
{
    private InfoCommandHandler $infoCommandHandler;

    private MockObject|TelegramBotService $botService;

    private MockObject|BotCommandRepository $commandRepository;

    private TelegramBot $bot;

    private TelegramMessage $message;

    private TelegramChat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        // 必须使用具体的 TelegramBotService 类的 Mock，因为：
        // 1. 该类包含复杂的 HTTP 请求逻辑，测试需要验证具体的消息发送行为
        // 2. 测试需要验证发送参数的正确性，依赖于具体的实现细节
        // 3. 业务逻辑要求确保消息格式化和发送的正确性
        $this->botService = $this->createMock(TelegramBotService::class);

        // 必须使用具体的 BotCommandRepository 类的 Mock，因为：
        // 1. 该类包含特定的数据库查询逻辑，测试需要验证查询方法的调用
        // 2. 测试需要验证查询参数的正确传递
        // 3. 业务逻辑依赖于具体的查询实现
        $this->commandRepository = $this->createMock(BotCommandRepository::class);

        $this->infoCommandHandler = new InfoCommandHandler(
            $this->botService,
            $this->commandRepository
        );

        $this->bot = new TelegramBot();

        $this->chat = new TelegramChat();
        $this->chat->setId(456);

        $this->message = new TelegramMessage();
        $this->message->setChat($this->chat);
    }

    public function testHandleWithValidParameters(): void
    {
        $command1 = new BotCommand();
        $command1->setCommand('test1');
        $command1->setDescription('Test command 1');

        $command2 = new BotCommand();
        $command2->setCommand('test2');
        $command2->setDescription('Test command 2');

        $commands = [$command1, $command2];

        $this->commandRepository->expects($this->once())
            ->method('getValidCommands')
            ->with($this->bot)
            ->willReturn($commands)
        ;

        $expectedResponse = "可用命令列表：\n\n";
        $expectedResponse .= "/info - 显示所有可用的命令\n";
        $expectedResponse .= "/test1 - Test command 1\n";
        $expectedResponse .= "/test2 - Test command 2\n";

        $this->botService->expects($this->once())
            ->method('sendMessage')
            ->with($this->bot, 456, $expectedResponse)
            ->willReturn(true)
        ;

        $this->infoCommandHandler->handle($this->bot, 'info', [], $this->message);
    }

    public function testHandleWithNoCommands(): void
    {
        $this->commandRepository->expects($this->once())
            ->method('getValidCommands')
            ->with($this->bot)
            ->willReturn([])
        ;

        $expectedResponse = "可用命令列表：\n\n";
        $expectedResponse .= "/info - 显示所有可用的命令\n";

        $this->botService->expects($this->once())
            ->method('sendMessage')
            ->with($this->bot, 456, $expectedResponse)
            ->willReturn(true)
        ;

        $this->infoCommandHandler->handle($this->bot, 'info', [], $this->message);
    }
}
