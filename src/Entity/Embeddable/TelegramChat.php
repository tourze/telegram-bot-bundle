<?php

namespace TelegramBotBundle\Entity\Embeddable;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;

#[ORM\Embeddable]
class TelegramChat implements PlainArrayInterface
{
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '聊天ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '聊天类型'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '名字'])]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '姓氏'])]
    private ?string $lastName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'username' => $this->getUsername(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
        ];
    }
}
