<?php

namespace TelegramBotBundle\Handler;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;

#[Autoconfigure(public: true)]
interface CommandHandlerInterface
{
    /**
     * 处理命令
     */
    public function handle(TelegramBot $bot, string $command, array $args, TelegramMessage $message): void;
}
