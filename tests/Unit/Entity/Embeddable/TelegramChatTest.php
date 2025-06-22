<?php

namespace TelegramBotBundle\Tests\Unit\Entity\Embeddable;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;

class TelegramChatTest extends TestCase
{
    private TelegramChat $chat;

    protected function setUp(): void
    {
        $this->chat = new TelegramChat();
    }

    public function test_setAndGetId_withValidId(): void
    {
        $id = 123456789;
        $this->chat->setId($id);
        $this->assertEquals($id, $this->chat->getId());
    }

    public function test_setAndGetId_withNull(): void
    {
        $this->chat->setId(null);
        $this->assertNull($this->chat->getId());
    }

    public function test_setAndGetId_withNegativeId(): void
    {
        $id = -123456789;
        $this->chat->setId($id);
        $this->assertEquals($id, $this->chat->getId());
    }

    public function test_setAndGetType_withPrivateType(): void
    {
        $type = 'private';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function test_setAndGetType_withGroupType(): void
    {
        $type = 'group';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function test_setAndGetType_withSupergroupType(): void
    {
        $type = 'supergroup';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function test_setAndGetType_withChannelType(): void
    {
        $type = 'channel';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function test_setAndGetType_withNull(): void
    {
        $this->chat->setType(null);
        $this->assertNull($this->chat->getType());
    }

    public function test_setAndGetTitle_withValidTitle(): void
    {
        $title = 'My Chat Group';
        $this->chat->setTitle($title);
        $this->assertEquals($title, $this->chat->getTitle());
    }

    public function test_setAndGetTitle_withEmptyString(): void
    {
        $this->chat->setTitle('');
        $this->assertEquals('', $this->chat->getTitle());
    }

    public function test_setAndGetTitle_withNull(): void
    {
        $this->chat->setTitle(null);
        $this->assertNull($this->chat->getTitle());
    }

    public function test_setAndGetTitle_withUnicodeCharacters(): void
    {
        $title = '我的聊天群组 🚀';
        $this->chat->setTitle($title);
        $this->assertEquals($title, $this->chat->getTitle());
    }

    public function test_setAndGetUsername_withValidUsername(): void
    {
        $username = 'test_user';
        $this->chat->setUsername($username);
        $this->assertEquals($username, $this->chat->getUsername());
    }

    public function test_setAndGetUsername_withEmptyString(): void
    {
        $this->chat->setUsername('');
        $this->assertEquals('', $this->chat->getUsername());
    }

    public function test_setAndGetUsername_withNull(): void
    {
        $this->chat->setUsername(null);
        $this->assertNull($this->chat->getUsername());
    }

    public function test_setAndGetFirstName_withValidName(): void
    {
        $firstName = 'John';
        $this->chat->setFirstName($firstName);
        $this->assertEquals($firstName, $this->chat->getFirstName());
    }

    public function test_setAndGetFirstName_withNull(): void
    {
        $this->chat->setFirstName(null);
        $this->assertNull($this->chat->getFirstName());
    }

    public function test_setAndGetFirstName_withUnicodeCharacters(): void
    {
        $firstName = '约翰';
        $this->chat->setFirstName($firstName);
        $this->assertEquals($firstName, $this->chat->getFirstName());
    }

    public function test_setAndGetLastName_withValidName(): void
    {
        $lastName = 'Doe';
        $this->chat->setLastName($lastName);
        $this->assertEquals($lastName, $this->chat->getLastName());
    }

    public function test_setAndGetLastName_withNull(): void
    {
        $this->chat->setLastName(null);
        $this->assertNull($this->chat->getLastName());
    }

    public function test_setAndGetLastName_withUnicodeCharacters(): void
    {
        $lastName = '多伊';
        $this->chat->setLastName($lastName);
        $this->assertEquals($lastName, $this->chat->getLastName());
    }

    public function test_defaultValues(): void
    {
        $chat = new TelegramChat();
        $this->assertNull($chat->getId());
        $this->assertNull($chat->getType());
        $this->assertNull($chat->getTitle());
        $this->assertNull($chat->getUsername());
        $this->assertNull($chat->getFirstName());
        $this->assertNull($chat->getLastName());
    }

    public function test_toArray_withCompleteData(): void
    {
        $this->chat->setId(123456789)
            ->setType('group')
            ->setTitle('Test Group')
            ->setUsername('test_group')
            ->setFirstName('John')
            ->setLastName('Doe');

        $array = $this->chat->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('firstName', $array);
        $this->assertArrayHasKey('lastName', $array);

        $this->assertEquals(123456789, $array['id']);
        $this->assertEquals('group', $array['type']);
        $this->assertEquals('Test Group', $array['title']);
        $this->assertEquals('test_group', $array['username']);
        $this->assertEquals('John', $array['firstName']);
        $this->assertEquals('Doe', $array['lastName']);
    }

    public function test_toArray_withMinimalData(): void
    {
        $array = $this->chat->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('firstName', $array);
        $this->assertArrayHasKey('lastName', $array);

        $this->assertNull($array['id']);
        $this->assertNull($array['type']);
        $this->assertNull($array['title']);
        $this->assertNull($array['username']);
        $this->assertNull($array['firstName']);
        $this->assertNull($array['lastName']);
    }

    public function test_retrievePlainArray(): void
    {
        $this->chat->setId(123456789)
            ->setType('private');

        $plainArray = $this->chat->retrievePlainArray();
        $toArray = $this->chat->toArray();

        $this->assertEquals($toArray, $plainArray);
    }

    public function test_fluentInterface(): void
    {
        $result = $this->chat
            ->setId(123456789)
            ->setType('private')
            ->setTitle('Private Chat')
            ->setUsername('john_doe')
            ->setFirstName('John')
            ->setLastName('Doe');

        $this->assertSame($this->chat, $result);
        $this->assertEquals(123456789, $this->chat->getId());
        $this->assertEquals('private', $this->chat->getType());
        $this->assertEquals('Private Chat', $this->chat->getTitle());
        $this->assertEquals('john_doe', $this->chat->getUsername());
        $this->assertEquals('John', $this->chat->getFirstName());
        $this->assertEquals('Doe', $this->chat->getLastName());
    }
} 