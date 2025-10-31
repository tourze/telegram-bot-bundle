<?php

namespace TelegramBotBundle\Entity\Embeddable;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Embeddable]
class TelegramUser implements PlainArrayInterface
{
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '用户ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否为机器人'])]
    private ?bool $isBot = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '用户名'])]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '姓氏'])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true, options: ['comment' => '语言代码'])]
    private ?string $languageCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getIsBot(): ?bool
    {
        return $this->isBot;
    }

    public function setIsBot(?bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(?string $languageCode): void
    {
        $this->languageCode = $languageCode;
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
            'isBot' => $this->getIsBot(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'username' => $this->getUsername(),
            'languageCode' => $this->getLanguageCode(),
        ];
    }
}
