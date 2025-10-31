<?php

namespace TelegramBotBundle\Tests\Entity\Embeddable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;

/**
 * @internal
 */
#[CoversClass(TelegramUser::class)]
final class TelegramUserTest extends TestCase
{
    private TelegramUser $user;

    protected function setUp(): void
    {
        $this->user = new TelegramUser();
    }

    public function testSetAndGetIdWithValidId(): void
    {
        $id = 123456789;
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function testSetAndGetIdWithNull(): void
    {
        $this->user->setId(null);
        $this->assertNull($this->user->getId());
    }

    public function testSetAndGetIdWithNegativeId(): void
    {
        $id = -123456789;
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function testSetAndGetIsBotWithTrue(): void
    {
        $this->user->setIsBot(true);
        $this->assertTrue($this->user->getIsBot());
    }

    public function testSetAndGetIsBotWithFalse(): void
    {
        $this->user->setIsBot(false);
        $this->assertFalse($this->user->getIsBot());
    }

    public function testSetAndGetIsBotWithNull(): void
    {
        $this->user->setIsBot(null);
        $this->assertNull($this->user->getIsBot());
    }

    public function testSetAndGetFirstNameWithValidName(): void
    {
        $firstName = 'John';
        $this->user->setFirstName($firstName);
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function testSetAndGetFirstNameWithEmptyString(): void
    {
        $this->user->setFirstName('');
        $this->assertEquals('', $this->user->getFirstName());
    }

    public function testSetAndGetFirstNameWithNull(): void
    {
        $this->user->setFirstName(null);
        $this->assertNull($this->user->getFirstName());
    }

    public function testSetAndGetFirstNameWithUnicodeCharacters(): void
    {
        $firstName = '约翰';
        $this->user->setFirstName($firstName);
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function testSetAndGetLastNameWithValidName(): void
    {
        $lastName = 'Doe';
        $this->user->setLastName($lastName);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function testSetAndGetLastNameWithEmptyString(): void
    {
        $this->user->setLastName('');
        $this->assertEquals('', $this->user->getLastName());
    }

    public function testSetAndGetLastNameWithNull(): void
    {
        $this->user->setLastName(null);
        $this->assertNull($this->user->getLastName());
    }

    public function testSetAndGetLastNameWithUnicodeCharacters(): void
    {
        $lastName = '多伊';
        $this->user->setLastName($lastName);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function testSetAndGetUsernameWithValidUsername(): void
    {
        $username = 'john_doe';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testSetAndGetUsernameWithEmptyString(): void
    {
        $this->user->setUsername('');
        $this->assertEquals('', $this->user->getUsername());
    }

    public function testSetAndGetUsernameWithNull(): void
    {
        $this->user->setUsername(null);
        $this->assertNull($this->user->getUsername());
    }

    public function testSetAndGetLanguageCodeWithValidCode(): void
    {
        $languageCode = 'en';
        $this->user->setLanguageCode($languageCode);
        $this->assertEquals($languageCode, $this->user->getLanguageCode());
    }

    public function testSetAndGetLanguageCodeWithComplexCode(): void
    {
        $languageCode = 'zh-CN';
        $this->user->setLanguageCode($languageCode);
        $this->assertEquals($languageCode, $this->user->getLanguageCode());
    }

    public function testSetAndGetLanguageCodeWithNull(): void
    {
        $this->user->setLanguageCode(null);
        $this->assertNull($this->user->getLanguageCode());
    }

    public function testSetAndGetLanguageCodeWithEmptyString(): void
    {
        $this->user->setLanguageCode('');
        $this->assertEquals('', $this->user->getLanguageCode());
    }

    public function testDefaultValues(): void
    {
        $user = new TelegramUser();
        $this->assertNull($user->getId());
        $this->assertNull($user->getIsBot());
        $this->assertNull($user->getFirstName());
        $this->assertNull($user->getLastName());
        $this->assertNull($user->getUsername());
        $this->assertNull($user->getLanguageCode());
    }

    public function testToArrayWithCompleteData(): void
    {
        $this->user->setId(123456789);
        $this->user->setIsBot(false);
        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');
        $this->user->setUsername('john_doe');
        $this->user->setLanguageCode('en');

        $array = $this->user->toArray();
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

    public function testToArrayWithBotData(): void
    {
        $this->user->setId(987654321);
        $this->user->setIsBot(true);
        $this->user->setFirstName('Test Bot');
        $this->user->setUsername('test_bot');

        $array = $this->user->toArray();

        $this->assertEquals(987654321, $array['id']);
        $this->assertTrue($array['isBot']);
        $this->assertEquals('Test Bot', $array['firstName']);
        $this->assertEquals('test_bot', $array['username']);
        $this->assertNull($array['lastName']);
        $this->assertNull($array['languageCode']);
    }

    public function testToArrayWithMinimalData(): void
    {
        $array = $this->user->toArray();
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

    public function testRetrievePlainArray(): void
    {
        $this->user->setId(123456789);
        $this->user->setFirstName('John');

        $plainArray = $this->user->retrievePlainArray();
        $toArray = $this->user->toArray();

        $this->assertEquals($toArray, $plainArray);
    }

    public function testSetterMethods(): void
    {
        $this->user->setId(123456789);
        $this->user->setIsBot(false);
        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');
        $this->user->setUsername('john_doe');
        $this->user->setLanguageCode('en');

        $this->assertEquals(123456789, $this->user->getId());
        $this->assertFalse($this->user->getIsBot());
        $this->assertEquals('John', $this->user->getFirstName());
        $this->assertEquals('Doe', $this->user->getLastName());
        $this->assertEquals('john_doe', $this->user->getUsername());
        $this->assertEquals('en', $this->user->getLanguageCode());
    }
}
