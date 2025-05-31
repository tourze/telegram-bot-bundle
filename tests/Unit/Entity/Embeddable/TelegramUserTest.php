<?php

namespace TelegramBotBundle\Tests\Unit\Entity\Embeddable;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;

class TelegramUserTest extends TestCase
{
    private TelegramUser $user;

    protected function setUp(): void
    {
        $this->user = new TelegramUser();
    }

    public function test_setAndGetId_withValidId(): void
    {
        $id = 123456789;
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function test_setAndGetId_withNull(): void
    {
        $this->user->setId(null);
        $this->assertNull($this->user->getId());
    }

    public function test_setAndGetId_withNegativeId(): void
    {
        $id = -123456789;
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function test_setAndGetIsBot_withTrue(): void
    {
        $this->user->setIsBot(true);
        $this->assertTrue($this->user->getIsBot());
    }

    public function test_setAndGetIsBot_withFalse(): void
    {
        $this->user->setIsBot(false);
        $this->assertFalse($this->user->getIsBot());
    }

    public function test_setAndGetIsBot_withNull(): void
    {
        $this->user->setIsBot(null);
        $this->assertNull($this->user->getIsBot());
    }

    public function test_setAndGetFirstName_withValidName(): void
    {
        $firstName = 'John';
        $this->user->setFirstName($firstName);
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function test_setAndGetFirstName_withEmptyString(): void
    {
        $this->user->setFirstName('');
        $this->assertEquals('', $this->user->getFirstName());
    }

    public function test_setAndGetFirstName_withNull(): void
    {
        $this->user->setFirstName(null);
        $this->assertNull($this->user->getFirstName());
    }

    public function test_setAndGetFirstName_withUnicodeCharacters(): void
    {
        $firstName = '约翰';
        $this->user->setFirstName($firstName);
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function test_setAndGetLastName_withValidName(): void
    {
        $lastName = 'Doe';
        $this->user->setLastName($lastName);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function test_setAndGetLastName_withEmptyString(): void
    {
        $this->user->setLastName('');
        $this->assertEquals('', $this->user->getLastName());
    }

    public function test_setAndGetLastName_withNull(): void
    {
        $this->user->setLastName(null);
        $this->assertNull($this->user->getLastName());
    }

    public function test_setAndGetLastName_withUnicodeCharacters(): void
    {
        $lastName = '多伊';
        $this->user->setLastName($lastName);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function test_setAndGetUsername_withValidUsername(): void
    {
        $username = 'john_doe';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function test_setAndGetUsername_withEmptyString(): void
    {
        $this->user->setUsername('');
        $this->assertEquals('', $this->user->getUsername());
    }

    public function test_setAndGetUsername_withNull(): void
    {
        $this->user->setUsername(null);
        $this->assertNull($this->user->getUsername());
    }

    public function test_setAndGetLanguageCode_withValidCode(): void
    {
        $languageCode = 'en';
        $this->user->setLanguageCode($languageCode);
        $this->assertEquals($languageCode, $this->user->getLanguageCode());
    }

    public function test_setAndGetLanguageCode_withComplexCode(): void
    {
        $languageCode = 'zh-CN';
        $this->user->setLanguageCode($languageCode);
        $this->assertEquals($languageCode, $this->user->getLanguageCode());
    }

    public function test_setAndGetLanguageCode_withNull(): void
    {
        $this->user->setLanguageCode(null);
        $this->assertNull($this->user->getLanguageCode());
    }

    public function test_setAndGetLanguageCode_withEmptyString(): void
    {
        $this->user->setLanguageCode('');
        $this->assertEquals('', $this->user->getLanguageCode());
    }

    public function test_defaultValues(): void
    {
        $user = new TelegramUser();
        $this->assertNull($user->getId());
        $this->assertNull($user->getIsBot());
        $this->assertNull($user->getFirstName());
        $this->assertNull($user->getLastName());
        $this->assertNull($user->getUsername());
        $this->assertNull($user->getLanguageCode());
    }

    public function test_toArray_withCompleteData(): void
    {
        $this->user->setId(123456789)
            ->setIsBot(false)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setUsername('john_doe')
            ->setLanguageCode('en');

        $array = $this->user->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('isBot', $array);
        $this->assertArrayHasKey('firstName', $array);
        $this->assertArrayHasKey('lastName', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('languageCode', $array);

        $this->assertEquals(123456789, $array['id']);
        $this->assertFalse($array['isBot']);
        $this->assertEquals('John', $array['firstName']);
        $this->assertEquals('Doe', $array['lastName']);
        $this->assertEquals('john_doe', $array['username']);
        $this->assertEquals('en', $array['languageCode']);
    }

    public function test_toArray_withBotData(): void
    {
        $this->user->setId(987654321)
            ->setIsBot(true)
            ->setFirstName('Test Bot')
            ->setUsername('test_bot');

        $array = $this->user->toArray();

        $this->assertEquals(987654321, $array['id']);
        $this->assertTrue($array['isBot']);
        $this->assertEquals('Test Bot', $array['firstName']);
        $this->assertEquals('test_bot', $array['username']);
        $this->assertNull($array['lastName']);
        $this->assertNull($array['languageCode']);
    }

    public function test_toArray_withMinimalData(): void
    {
        $array = $this->user->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('isBot', $array);
        $this->assertArrayHasKey('firstName', $array);
        $this->assertArrayHasKey('lastName', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('languageCode', $array);

        $this->assertNull($array['id']);
        $this->assertNull($array['isBot']);
        $this->assertNull($array['firstName']);
        $this->assertNull($array['lastName']);
        $this->assertNull($array['username']);
        $this->assertNull($array['languageCode']);
    }

    public function test_retrievePlainArray(): void
    {
        $this->user->setId(123456789)
            ->setFirstName('John');

        $plainArray = $this->user->retrievePlainArray();
        $toArray = $this->user->toArray();

        $this->assertEquals($toArray, $plainArray);
    }

    public function test_fluentInterface(): void
    {
        $result = $this->user
            ->setId(123456789)
            ->setIsBot(false)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setUsername('john_doe')
            ->setLanguageCode('en');

        $this->assertSame($this->user, $result);
        $this->assertEquals(123456789, $this->user->getId());
        $this->assertFalse($this->user->getIsBot());
        $this->assertEquals('John', $this->user->getFirstName());
        $this->assertEquals('Doe', $this->user->getLastName());
        $this->assertEquals('john_doe', $this->user->getUsername());
        $this->assertEquals('en', $this->user->getLanguageCode());
    }
} 