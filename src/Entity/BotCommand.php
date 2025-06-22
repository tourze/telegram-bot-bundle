<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TelegramBotBundle\Repository\BotCommandRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: BotCommandRepository::class)]
#[ORM\Table(name: 'telegram_bot_command', options: ['comment' => 'Telegram机器人命令'])]
#[ORM\Index(columns: ['bot_id', 'command'], name: 'telegram_bot_command_bot_command')]
class BotCommand implements PlainArrayInterface, \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class)]
    #[ORM\JoinColumn(name: 'bot_id', referencedColumnName: 'id', nullable: false, options: ['comment' => 'TG机器人'])]
    private TelegramBot $bot;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '命令名称'])]
    private string $command = '';

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '命令处理器类'])]
    private string $handler = '';

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '命令描述'])]
    private string $description = '';

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;


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

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getHandler(): string
    {
        return $this->handler;
    }

    public function setHandler(string $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

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
            'handler' => $this->getHandler(),
            'description' => $this->getDescription(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'createdBy' => $this->getCreatedBy(),
            'updatedBy' => $this->getUpdatedBy(),
        ];
    }
    
    public function __toString(): string
    {
        return sprintf('%s #%s', $this->command ?: 'BotCommand', $this->id ?? 'new');
    }
}
