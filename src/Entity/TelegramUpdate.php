<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;

#[ORM\Entity(repositoryClass: TelegramUpdateRepository::class)]
#[ORM\Table(name: 'telegram_update', options: ['comment' => 'Telegram更新消息'])]
#[ORM\Index(columns: ['bot_id', 'update_id'], name: 'telegram_update_bot_update')]
class TelegramUpdate implements PlainArrayInterface
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = '0';

    #[ListColumn(title: 'TG机器人')]
    #[FormField(title: 'TG机器人')]
    #[ORM\ManyToOne(targetEntity: TelegramBot::class)]
    #[ORM\JoinColumn(name: 'bot_id', referencedColumnName: 'id', nullable: false, options: ['comment' => 'TG机器人'])]
    private TelegramBot $bot;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => 'Telegram更新ID'])]
    private string $updateId;

    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $message = null;

    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $editedMessage = null;

    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $channelPost = null;

    #[ORM\Embedded(class: TelegramMessage::class)]
    private ?TelegramMessage $editedChannelPost = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    private ?array $rawData = null;

    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getBot(): TelegramBot
    {
        return $this->bot;
    }

    public function setBot(TelegramBot $bot): self
    {
        $this->bot = $bot;

        return $this;
    }

    public function getUpdateId(): string
    {
        return $this->updateId;
    }

    public function setUpdateId(string $updateId): self
    {
        $this->updateId = $updateId;

        return $this;
    }

    public function getMessage(): ?TelegramMessage
    {
        return $this->message;
    }

    public function setMessage(?TelegramMessage $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEditedMessage(): ?TelegramMessage
    {
        return $this->editedMessage;
    }

    public function setEditedMessage(?TelegramMessage $editedMessage): self
    {
        $this->editedMessage = $editedMessage;

        return $this;
    }

    public function getChannelPost(): ?TelegramMessage
    {
        return $this->channelPost;
    }

    public function setChannelPost(?TelegramMessage $channelPost): self
    {
        $this->channelPost = $channelPost;

        return $this;
    }

    public function getEditedChannelPost(): ?TelegramMessage
    {
        return $this->editedChannelPost;
    }

    public function setEditedChannelPost(?TelegramMessage $editedChannelPost): self
    {
        $this->editedChannelPost = $editedChannelPost;

        return $this;
    }

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(?array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function retrievePlainArray(): array
    {
        return $this->toArray();
    }

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
        ];
    }
}
