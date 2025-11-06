<?php

namespace TelegramBotBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;

/**
 * Telegram Bot Update 事件
 *
 * 参考文档: https://core.telegram.org/bots/api#update
 */
class TelegramUpdateEvent extends Event
{
    public function __construct(
        private readonly TelegramBot $bot,
        private readonly TelegramUpdate $update,
    ) {
    }

    public function getBot(): TelegramBot
    {
        return $this->bot;
    }

    public function getUpdate(): TelegramUpdate
    {
        return $this->update;
    }

    /**
     * @return array<mixed>|null
     */
    public function getMessage(): ?array
    {
        $rawData = $this->update->getRawData();
        $message = $rawData['message'] ?? null;

        return is_array($message) ? $message : null;
    }

    public function getMessageText(): ?string
    {
        return $this->update->getMessage()?->getText();
    }

    public function getChatId(): ?int
    {
        return $this->update->getMessage()?->getChat()?->getId();
    }

    /**
     * @return array<mixed>|null
     */
    public function getCallbackQuery(): ?array
    {
        $rawData = $this->update->getRawData();
        $callbackQuery = $rawData['callback_query'] ?? null;

        return is_array($callbackQuery) ? $callbackQuery : null;
    }
}
