<?php

namespace TelegramBotBundle\Tests\Unit\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Handler\InfoCommandHandler;
use TelegramBotBundle\Repository\BotCommandRepository;
use TelegramBotBundle\Service\TelegramBotService;

class InfoCommandHandlerTest extends TestCase
{
    private InfoCommandHandler $infoCommandHandler;
    private MockObject|TelegramBotService $botService;
    private MockObject|BotCommandRepository $commandRepository;
    private TelegramBot $bot;
    private TelegramMessage $message;
    private TelegramChat $chat;

    protected function setUp(): void
    {
        $this->botService = $this->createMock(TelegramBotService::class);
        $this->commandRepository = $this->createMock(BotCommandRepository::class);

        $this->infoCommandHandler = new InfoCommandHandler(
            $this->botService,
            $this->commandRepository
        );

        $this->bot = new TelegramBot();

        $this->chat = new TelegramChat();
        $this->chat->setId('456');

        $this->message = new TelegramMessage();
        $this->message->setChat($this->chat);
    }

    public function testHandle_withValidParameters(): void
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
            ->willReturn($commands);

        $expectedResponse = "可用命令列表：\n\n";
        $expectedResponse .= "/info - 显示所有可用的命令\n";
        $expectedResponse .= "/test1 - Test command 1\n";
        $expectedResponse .= "/test2 - Test command 2\n";

        $this->botService->expects($this->once())
            ->method('sendMessage')
            ->with($this->bot, '456', $expectedResponse)
            ->willReturn(true);

        $this->infoCommandHandler->handle($this->bot, 'info', [], $this->message);
    }

    public function testHandle_withNoCommands(): void
    {
        $this->commandRepository->expects($this->once())
            ->method('getValidCommands')
            ->with($this->bot)
            ->willReturn([]);

        $expectedResponse = "可用命令列表：\n\n";
        $expectedResponse .= "/info - 显示所有可用的命令\n";

        $this->botService->expects($this->once())
            ->method('sendMessage')
            ->with($this->bot, '456', $expectedResponse)
            ->willReturn(true);

        $this->infoCommandHandler->handle($this->bot, 'info', [], $this->message);
    }
}
