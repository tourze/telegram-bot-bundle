<?php

namespace TelegramBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: TelegramBot::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private TelegramBot $bot;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '规则名称'])]
    private string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '匹配关键词'])]
    private string $keyword = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 4000)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '回复内容'])]
    private string $replyContent = '';

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否精确匹配', 'default' => false])]
    private bool $exactMatch = false;

    #[Assert\Type(type: 'int')]
    #[Assert\GreaterThanOrEqual(value: 0)]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '优先级', 'default' => 0])]
    private int $priority = 0;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getReplyContent(): string
    {
        return $this->replyContent;
    }

    public function setReplyContent(string $replyContent): void
    {
        $this->replyContent = $replyContent;
    }

    public function isExactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function setExactMatch(bool $exactMatch): void
    {
        $this->exactMatch = $exactMatch;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'AutoReplyRule', 0 === $this->id ? 'new' : $this->id);
    }
}
