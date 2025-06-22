<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TelegramBotBundle\Repository\CommandLogRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;

#[ORM\Entity(repositoryClass: CommandLogRepository::class)]
#[ORM\Table(name: 'telegram_command_log', options: ['comment' => 'Telegram命令执行日志'])]
#[ORM\Index(columns: ['bot_id', 'command', 'create_time'], name: 'telegram_command_log_bot_command_time')]
class CommandLog implements PlainArrayInterface, \Stringable
{
    use CreateTimeAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class)]
    #[ORM\JoinColumn(name: 'bot_id', referencedColumnName: 'id', nullable: false, options: ['comment' => 'TG机器人'])]
    private TelegramBot $bot;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '命令名称'])]
    private string $command = '';

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '命令参数'])]
    private ?array $args = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为系统命令'])]
    private bool $isSystem = false;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '用户ID'])]
    private ?int $userId = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '聊天ID'])]
    private ?int $chatId = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '聊天类型'])]
    private ?string $chatType = null;


    public function getId(): ?int
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

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getArgs(): ?array
    {
        return $this->args;
    }

    public function setArgs(?array $args): self
    {
        $this->args = $args;

        return $this;
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(bool $isSystem): self
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function setChatId(?int $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getChatType(): ?string
    {
        return $this->chatType;
    }

    public function setChatType(?string $chatType): self
    {
        $this->chatType = $chatType;

        return $this;
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
            'command' => $this->getCommand(),
            'args' => $this->getArgs(),
            'isSystem' => $this->isSystem(),
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'chatId' => $this->getChatId(),
            'chatType' => $this->getChatType(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
        ];
    }
    
    public function __toString(): string
    {
        return sprintf('CommandLog #%s: %s', $this->id ?? 'new', $this->command ?: 'no-command');
    }
}
