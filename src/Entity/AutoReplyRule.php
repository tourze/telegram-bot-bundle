<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TelegramBotBundle\Repository\AutoReplyRuleRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: AutoReplyRuleRepository::class)]
#[ORM\Table(name: 'tg_auto_reply_rule', options: ['comment' => 'Telegram自动回复规则'])]
class AutoReplyRule implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class)]
    #[ORM\JoinColumn(nullable: false)]
    private TelegramBot $bot;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '规则名称'])]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '匹配关键词'])]
    private string $keyword = '';

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '回复内容'])]
    private string $replyContent = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否精确匹配', 'default' => false])]
    private bool $exactMatch = false;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '优先级', 'default' => 0])]
    private int $priority = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;


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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getReplyContent(): string
    {
        return $this->replyContent;
    }

    public function setReplyContent(string $replyContent): self
    {
        $this->replyContent = $replyContent;

        return $this;
    }

    public function isExactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function setExactMatch(bool $exactMatch): self
    {
        $this->exactMatch = $exactMatch;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

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


    public function __toString(): string
    {
        return sprintf('%s #%s', 'AutoReplyRule', $this->id ?? 'new');
    }
}
