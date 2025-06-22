<?php

namespace TelegramBotBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TelegramBotBundle\Entity\CommandLog;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Handler\CommandHandlerInterface;
use TelegramBotBundle\Handler\InfoCommandHandler;
use TelegramBotBundle\Repository\BotCommandRepository;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;

/**
 * 命令解析服务
 */
class CommandParserService
{
    /**
     * 系统命令列表
     */
    private const SYSTEM_COMMANDS = [
        'info' => [
            'description' => '显示所有可用的命令',
            'handler' => InfoCommandHandler::class,
        ],
    ];

    public function __construct(
        private readonly BotCommandRepository $commandRepository,
        private readonly DoctrineService $doctrineService,
        private readonly LoggerInterface $logger,
        #[Autowire(service: 'service_container')] private readonly ContainerInterface $container,
    ) {
    }

    /**
     * 解析并分发命令
     */
    public function parseAndDispatch(TelegramBot $bot, TelegramMessage $message): void
    {
        $text = $message->getText();
        if (null === $text || '' === trim($text) || !str_starts_with($text, '/')) {
            return;
        }

        // 解析命令和参数
        $parts = explode(' ', $text);
        $commandText = substr($parts[0], 1); // 去掉开头的 /

        // 移除@BotUsername部分（如果有）
        if (str_contains($commandText, '@')) {
            [$commandText] = explode('@', $commandText);
        }

        $args = array_slice($parts, 1);

        // 如果是系统命令，直接分发
        if (isset(self::SYSTEM_COMMANDS[$commandText])) {
            $this->logger->info('执行系统命令', [
                'bot' => $bot->getId(),
                'command' => $commandText,
                'args' => $args,
                'message' => $message->toArray(),
            ]);

            $this->logCommand($bot, $commandText, $args, $message, true);

            $handler = $this->container->get(self::SYSTEM_COMMANDS[$commandText]['handler']);
            /* @var CommandHandlerInterface $handler */
            $handler->handle($bot, $commandText, $args, $message);

            return;
        }

        // 检查命令是否存在且有效
        $command = $this->commandRepository->findCommand($bot, $commandText);
        if (null === $command) {
            $this->logger->info('未知的命令', [
                'bot' => $bot->getId(),
                'command' => $commandText,
                'message' => $message->toArray(),
            ]);

            return;
        }

        $this->logger->info('执行自定义命令', [
            'bot' => $bot->getId(),
            'command' => $command->toArray(),
            'args' => $args,
            'message' => $message->toArray(),
        ]);

        $this->logCommand($bot, $commandText, $args, $message, false);

        // 使用配置的handler处理命令
        $handler = $this->container->get($command->getHandler());
        /* @var CommandHandlerInterface $handler */
        $handler->handle($bot, $command->getCommand(), $args, $message);
    }

    /**
     * 记录命令执行日志
     */
    private function logCommand(TelegramBot $bot, string $command, array $args, TelegramMessage $message, bool $isSystem): void
    {
        $log = new CommandLog();
        $log->setBot($bot)
            ->setCommand($command)
            ->setArgs($args)
            ->setIsSystem($isSystem);

        $from = $message->getFrom();
        if (null !== $from) {
            $log->setUserId($from->getId())
                ->setUsername($from->getUsername());
        }

        $chat = $message->getChat();
        if (null !== $chat) {
            $log->setChatId($chat->getId())
                ->setChatType($chat->getType());
        }

        $this->doctrineService->asyncInsert($log);
    }
}
