<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TelegramBotBundle\Repository\CommandLogRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: CommandLogRepository::class)]
#[ORM\Table(name: 'telegram_command_log', options: ['comment' => 'Telegram命令执行日志'])]
#[ORM\Index(columns: ['bot_id', 'command', 'create_time'], name: 'telegram_command_log_bot_command_time')]
class CommandLog implements PlainArrayInterface, \Stringable
{
    use CreateTimeAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'bot_id', referencedColumnName: 'id', nullable: false, options: ['comment' => 'TG机器人'])]
    private TelegramBot $bot;

    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '命令名称'])]
    private string $command = '';

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '命令参数'])]
    private ?array $args = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为系统命令'])]
    private bool $isSystem = false;

    #[Assert\Type(type: 'int')]
    #[Assert\Positive]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '用户ID'])]
    private ?int $userId = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '聊天ID'])]
    private ?int $chatId = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '聊天类型'])]
    private ?string $chatType = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBot(): TelegramBot
    {
        return $this->bot;
    }

    public function setBot(TelegramBot $bot): void
    {
        $this->bot = $bot;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return array<mixed>|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * @param array<mixed>|null $args
     */
    public function setArgs(?array $args): void
    {
        $this->args = $args;
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(bool $isSystem): void
    {
        $this->isSystem = $isSystem;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function setChatId(?int $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getChatType(): ?string
    {
        return $this->chatType;
    }

    public function setChatType(?string $chatType): void
    {
        $this->chatType = $chatType;
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
        return sprintf('CommandLog #%s: %s', 0 === $this->id ? 'new' : $this->id, '' !== $this->command ? $this->command : 'no-command');
    }
}
