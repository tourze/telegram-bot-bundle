<?php

namespace TelegramBotBundle\Handler;

use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\BotCommandRepository;
use TelegramBotBundle\Service\TelegramBotService;

/**
 * 处理 /info 命令，显示所有可用命令
 */
class InfoCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TelegramBotService $botService,
        private readonly BotCommandRepository $commandRepository,
    ) {
    }

    public function handle(TelegramBot $bot, string $command, array $args, TelegramMessage $message): void
    {
        $commands = $this->commandRepository->getValidCommands($bot);

        $response = "可用命令列表：\n\n";
        // 先显示系统命令
        $response .= "/info - 显示所有可用的命令\n";

        // 再显示配置的命令
        if ($commands) {
            foreach ($commands as $_command) {
                $response .= sprintf("/%s - %s\n", $_command->getCommand(), $_command->getDescription());
            }
        }

        $this->botService->sendMessage(
            $bot,
            (string) $message->getChat()?->getId(),
            $response
        );
    }
}
