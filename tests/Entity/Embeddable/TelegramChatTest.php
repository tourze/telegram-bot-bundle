<?php

namespace TelegramBotBundle\Tests\Entity\Embeddable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;

/**
 * @internal
 */
#[CoversClass(TelegramChat::class)]
final class TelegramChatTest extends TestCase
{
    private TelegramChat $chat;

    protected function setUp(): void
    {
        $this->chat = new TelegramChat();
    }

    public function testSetAndGetIdWithValidId(): void
    {
        $id = 123456789;
        $this->chat->setId($id);
        $this->assertEquals($id, $this->chat->getId());
    }

    public function testSetAndGetIdWithNull(): void
    {
        $this->chat->setId(null);
        $this->assertNull($this->chat->getId());
    }

    public function testSetAndGetIdWithNegativeId(): void
    {
        $id = -123456789;
        $this->chat->setId($id);
        $this->assertEquals($id, $this->chat->getId());
    }

    public function testSetAndGetTypeWithPrivateType(): void
    {
        $type = 'private';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function testSetAndGetTypeWithGroupType(): void
    {
        $type = 'group';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function testSetAndGetTypeWithSupergroupType(): void
    {
        $type = 'supergroup';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function testSetAndGetTypeWithChannelType(): void
    {
        $type = 'channel';
        $this->chat->setType($type);
        $this->assertEquals($type, $this->chat->getType());
    }

    public function testSetAndGetTypeWithNull(): void
    {
        $this->chat->setType(null);
        $this->assertNull($this->chat->getType());
    }

    public function testSetAndGetTitleWithValidTitle(): void
    {
        $title = 'My Chat Group';
        $this->chat->setTitle($title);
        $this->assertEquals($title, $this->chat->getTitle());
    }

    public function testSetAndGetTitleWithEmptyString(): void
    {
        $this->chat->setTitle('');
        $this->assertEquals('', $this->chat->getTitle());
    }

    public function testSetAndGetTitleWithNull(): void
    {
        $this->chat->setTitle(null);
        $this->assertNull($this->chat->getTitle());
    }

    public function testSetAndGetTitleWithUnicodeCharacters(): void
    {
        $title = '我的聊天群组 🚀';
        $this->chat->setTitle($title);
        $this->assertEquals($title, $this->chat->getTitle());
    }

    public function testSetAndGetUsernameWithValidUsername(): void
    {
        $username = 'test_user';
        $this->chat->setUsername($username);
        $this->assertEquals($username, $this->chat->getUsername());
    }

    public function testSetAndGetUsernameWithEmptyString(): void
    {
        $this->chat->setUsername('');
        $this->assertEquals('', $this->chat->getUsername());
    }

    public function testSetAndGetUsernameWithNull(): void
    {
        $this->chat->setUsername(null);
        $this->assertNull($this->chat->getUsername());
    }

    public function testSetAndGetFirstNameWithValidName(): void
    {
        $firstName = 'John';
        $this->chat->setFirstName($firstName);
        $this->assertEquals($firstName, $this->chat->getFirstName());
    }

    public function testSetAndGetFirstNameWithNull(): void
    {
        $this->chat->setFirstName(null);
        $this->assertNull($this->chat->getFirstName());
    }

    public function testSetAndGetFirstNameWithUnicodeCharacters(): void
    {
        $firstName = '约翰';
        $this->chat->setFirstName($firstName);
        $this->assertEquals($firstName, $this->chat->getFirstName());
    }

    public function testSetAndGetLastNameWithValidName(): void
    {
        $lastName = 'Doe';
        $this->chat->setLastName($lastName);
        $this->assertEquals($lastName, $this->chat->getLastName());
    }

    public function testSetAndGetLastNameWithNull(): void
    {
        $this->chat->setLastName(null);
        $this->assertNull($this->chat->getLastName());
    }

    public function testSetAndGetLastNameWithUnicodeCharacters(): void
    {
        $lastName = '多伊';
        $this->chat->setLastName($lastName);
        $this->assertEquals($lastName, $this->chat->getLastName());
    }

    public function testDefaultValues(): void
    {
        $chat = new TelegramChat();
        $this->assertNull($chat->getId());
        $this->assertNull($chat->getType());
        $this->assertNull($chat->getTitle());
        $this->assertNull($chat->getUsername());
        $this->assertNull($chat->getFirstName());
        $this->assertNull($chat->getLastName());
    }

    public function testToArrayWithCompleteData(): void
    {
        $this->chat->setId(123456789);
        $this->chat->setType('group');
        $this->chat->setTitle('Test Group');
        $this->chat->setUsername('test_group');
        $this->chat->setFirstName('John');
        $this->chat->setLastName('Doe');

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

    public function testToArrayWithMinimalData(): void
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

    public function testRetrievePlainArray(): void
    {
        $this->chat->setId(123456789);
        $this->chat->setType('private');

        $plainArray = $this->chat->retrievePlainArray();
        $toArray = $this->chat->toArray();

        $this->assertEquals($toArray, $plainArray);
    }

    public function testSetterMethods(): void
    {
        $this->chat->setId(123456789);
        $this->chat->setType('private');
        $this->chat->setTitle('Private Chat');
        $this->chat->setUsername('john_doe');
        $this->chat->setFirstName('John');
        $this->chat->setLastName('Doe');

        $this->assertEquals(123456789, $this->chat->getId());
        $this->assertEquals('private', $this->chat->getType());
        $this->assertEquals('Private Chat', $this->chat->getTitle());
        $this->assertEquals('john_doe', $this->chat->getUsername());
        $this->assertEquals('John', $this->chat->getFirstName());
        $this->assertEquals('Doe', $this->chat->getLastName());
    }
}
