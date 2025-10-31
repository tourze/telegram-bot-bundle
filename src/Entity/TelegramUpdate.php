<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: TelegramUpdateRepository::class)]
#[ORM\Table(name: 'telegram_update', options: ['comment' => 'Telegram更新消息'])]
#[ORM\Index(columns: ['bot_id', 'update_id'], name: 'telegram_update_bot_update')]
class TelegramUpdate implements PlainArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use CreateTimeAware;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'bot_id', referencedColumnName: 'id', nullable: false, options: ['comment' => 'TG机器人'])]
    private TelegramBot $bot;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(name: 'update_id', type: Types::BIGINT, options: ['comment' => 'Telegram更新ID'])]
    private string $updateId;

    #[Assert\Valid]
    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $message = null;

    #[Assert\Valid]
    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $editedMessage = null;

    #[Assert\Valid]
    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $channelPost = null;

    #[Assert\Valid]
    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $editedChannelPost = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    private ?array $rawData = null;

    public function getBot(): TelegramBot
    {
        return $this->bot;
    }

    public function setBot(TelegramBot $bot): void
    {
        $this->bot = $bot;
    }

    public function getUpdateId(): string
    {
        return $this->updateId;
    }

    public function setUpdateId(string $updateId): void
    {
        $this->updateId = $updateId;
    }

    public function getMessage(): ?TelegramMessage
    {
        return $this->message;
    }

    public function setMessage(?TelegramMessage $message): void
    {
        $this->message = $message;
    }

    public function getEditedMessage(): ?TelegramMessage
    {
        return $this->editedMessage;
    }

    public function setEditedMessage(?TelegramMessage $editedMessage): void
    {
        $this->editedMessage = $editedMessage;
    }

    public function getChannelPost(): ?TelegramMessage
    {
        return $this->channelPost;
    }

    public function setChannelPost(?TelegramMessage $channelPost): void
    {
        $this->channelPost = $channelPost;
    }

    public function getEditedChannelPost(): ?TelegramMessage
    {
        return $this->editedChannelPost;
    }

    public function setEditedChannelPost(?TelegramMessage $editedChannelPost): void
    {
        $this->editedChannelPost = $editedChannelPost;
    }

    /**
     * @return array<mixed>|null
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    /**
     * @param array<mixed>|null $rawData
     */
    public function setRawData(?array $rawData): void
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'bot' => $this->getBot()->toArray(),
            'updateId' => $this->getUpdateId(),
            'message' => $this->getMessage()?->toArray(),
            'editedMessage' => $this->getEditedMessage()?->toArray(),
            'channelPost' => $this->getChannelPost()?->toArray(),
            'editedChannelPost' => $this->getEditedChannelPost()?->toArray(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('TelegramUpdate #%s (Update ID: %s)', $this->id ?? 'new', $this->updateId ?? 'n/a');
    }
}
