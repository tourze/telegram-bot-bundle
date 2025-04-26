<?php

namespace TelegramBotBundle\Entity\Embeddable;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;

#[ORM\Embeddable]
class TelegramMessage implements PlainArrayInterface
{
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '消息ID'])]
    private ?int $messageId = null;

    #[ORM\Embedded(class: TelegramUser::class)]
    private ?TelegramUser $from = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '消息时间戳'])]
    private ?int $date = null;

    #[ORM\Embedded(class: TelegramChat::class)]
    private ?TelegramChat $chat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '消息文本'])]
    private ?string $text = null;

    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    public function setMessageId(?int $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getFrom(): ?TelegramUser
    {
        return $this->from;
    }

    public function setFrom(?TelegramUser $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(?int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getChat(): ?TelegramChat
    {
        return $this->chat;
    }

    public function setChat(?TelegramChat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function retrievePlainArray(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'messageId' => $this->getMessageId(),
            'from' => $this->getFrom()?->toArray(),
            'date' => $this->getDate(),
            'chat' => $this->getChat()?->toArray(),
            'text' => $this->getText(),
        ];
    }
}
