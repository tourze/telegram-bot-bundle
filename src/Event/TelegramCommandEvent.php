<?php

namespace TelegramBotBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram Bot 命令事件
 */
class TelegramCommandEvent extends Event
{
    public function __construct(
        private readonly TelegramBot $bot,
        private readonly string $command,
        private readonly array $args,
        private readonly TelegramMessage $message,
    ) {
    }

    public function getBot(): TelegramBot
    {
        return $this->bot;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getMessage(): TelegramMessage
    {
        return $this->message;
    }

    public function getChatId(): ?int
    {
        return $this->message->getChat()?->getId();
    }
}
