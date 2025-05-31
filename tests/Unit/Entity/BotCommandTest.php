<?php

namespace TelegramBotBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\TelegramBot;

class BotCommandTest extends TestCase
{
    private BotCommand $botCommand;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        $this->botCommand = new BotCommand();
        $this->bot = new TelegramBot();
    }

    public function test_setAndGetBot_withValidBot(): void
    {
        $this->botCommand->setBot($this->bot);
        $this->assertSame($this->bot, $this->botCommand->getBot());
    }

    public function test_setAndGetCommand_withValidCommand(): void
    {
        $command = 'start';
        $this->botCommand->setCommand($command);
        $this->assertEquals($command, $this->botCommand->getCommand());
    }

    public function test_setAndGetCommand_withEmptyString(): void
    {
        $this->botCommand->setCommand('');
        $this->assertEquals('', $this->botCommand->getCommand());
    }

    public function test_setAndGetCommand_withSpecialCharacters(): void
    {
        $command = 'test_command123';
        $this->botCommand->setCommand($command);
        $this->assertEquals($command, $this->botCommand->getCommand());
    }

    public function test_setAndGetHandler_withValidHandler(): void
    {
        $handler = 'App\\Handler\\StartCommandHandler';
        $this->botCommand->setHandler($handler);
        $this->assertEquals($handler, $this->botCommand->getHandler());
    }

    public function test_setAndGetHandler_withEmptyString(): void
    {
        $this->botCommand->setHandler('');
        $this->assertEquals('', $this->botCommand->getHandler());
    }

    public function test_setAndGetDescription_withValidDescription(): void
    {
        $description = 'Start the bot';
        $this->botCommand->setDescription($description);
        $this->assertEquals($description, $this->botCommand->getDescription());
    }

    public function test_setAndGetDescription_withEmptyString(): void
    {
        $this->botCommand->setDescription('');
        $this->assertEquals('', $this->botCommand->getDescription());
    }

    public function test_setAndGetDescription_withLongDescription(): void
    {
        $description = str_repeat('a', 255);
        $this->botCommand->setDescription($description);
        $this->assertEquals($description, $this->botCommand->getDescription());
    }

    public function test_setAndGetValid_withTrue(): void
    {
        $this->botCommand->setValid(true);
        $this->assertTrue($this->botCommand->isValid());
    }

    public function test_setAndGetValid_withFalse(): void
    {
        $this->botCommand->setValid(false);
        $this->assertFalse($this->botCommand->isValid());
    }

    public function test_setAndGetValid_withNull(): void
    {
        $this->botCommand->setValid(null);
        $this->assertNull($this->botCommand->isValid());
    }

    public function test_setAndGetCreatedBy_withValidString(): void
    {
        $createdBy = 'admin_user';
        $this->botCommand->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->botCommand->getCreatedBy());
    }

    public function test_setAndGetCreatedBy_withNull(): void
    {
        $this->botCommand->setCreatedBy(null);
        $this->assertNull($this->botCommand->getCreatedBy());
    }

    public function test_setAndGetUpdatedBy_withValidString(): void
    {
        $updatedBy = 'editor_user';
        $this->botCommand->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->botCommand->getUpdatedBy());
    }

    public function test_setAndGetUpdatedBy_withNull(): void
    {
        $this->botCommand->setUpdatedBy(null);
        $this->assertNull($this->botCommand->getUpdatedBy());
    }

    public function test_setAndGetCreateTime_withValidDateTime(): void
    {
        $dateTime = new \DateTime('2023-01-01 10:00:00');
        $this->botCommand->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->botCommand->getCreateTime());
    }

    public function test_setAndGetCreateTime_withNull(): void
    {
        $this->botCommand->setCreateTime(null);
        $this->assertNull($this->botCommand->getCreateTime());
    }

    public function test_setAndGetUpdateTime_withValidDateTime(): void
    {
        $dateTime = new \DateTime('2023-01-01 11:00:00');
        $this->botCommand->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->botCommand->getUpdateTime());
    }

    public function test_setAndGetUpdateTime_withNull(): void
    {
        $this->botCommand->setUpdateTime(null);
        $this->assertNull($this->botCommand->getUpdateTime());
    }

    public function test_defaultValues(): void
    {
        $command = new BotCommand();
        $this->assertNull($command->getId());
        $this->assertEquals('', $command->getCommand());
        $this->assertEquals('', $command->getHandler());
        $this->assertEquals('', $command->getDescription());
        $this->assertFalse($command->isValid());
        $this->assertNull($command->getCreatedBy());
        $this->assertNull($command->getUpdatedBy());
        $this->assertNull($command->getCreateTime());
        $this->assertNull($command->getUpdateTime());
    }

    public function test_toArray_withCompleteData(): void
    {
        $this->bot->setName('Test Bot');
        $this->bot->setUsername('test_bot');
        $this->bot->setToken('test_token');
        
        $createTime = new \DateTime('2023-01-01 10:00:00');
        $updateTime = new \DateTime('2023-01-01 11:00:00');
        
        $this->botCommand->setBot($this->bot)
            ->setCommand('start')
            ->setHandler('App\\Handler\\StartCommandHandler')
            ->setDescription('Start the bot')
            ->setValid(true)
            ->setCreatedBy('admin')
            ->setUpdatedBy('editor');
        
        $this->botCommand->setCreateTime($createTime);
        $this->botCommand->setUpdateTime($updateTime);

        $array = $this->botCommand->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('bot', $array);
        $this->assertArrayHasKey('command', $array);
        $this->assertArrayHasKey('handler', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('valid', $array);
        $this->assertArrayHasKey('createTime', $array);
        $this->assertArrayHasKey('updateTime', $array);
        $this->assertArrayHasKey('createdBy', $array);
        $this->assertArrayHasKey('updatedBy', $array);

        $this->assertEquals('start', $array['command']);
        $this->assertEquals('App\\Handler\\StartCommandHandler', $array['handler']);
        $this->assertEquals('Start the bot', $array['description']);
        $this->assertTrue($array['valid']);
        $this->assertEquals('admin', $array['createdBy']);
        $this->assertEquals('editor', $array['updatedBy']);
        $this->assertEquals('2023-01-01 10:00:00', $array['createTime']);
        $this->assertEquals('2023-01-01 11:00:00', $array['updateTime']);
    }

    public function test_toArray_withMinimalData(): void
    {
        $this->botCommand->setBot($this->bot);
        $array = $this->botCommand->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('createTime', $array);
        $this->assertArrayHasKey('updateTime', $array);
        $this->assertArrayHasKey('createdBy', $array);
        $this->assertArrayHasKey('updatedBy', $array);
        $this->assertNull($array['createTime']);
        $this->assertNull($array['updateTime']);
        $this->assertNull($array['createdBy']);
        $this->assertNull($array['updatedBy']);
    }

    public function test_retrievePlainArray(): void
    {
        $this->botCommand->setBot($this->bot);
        $plainArray = $this->botCommand->retrievePlainArray();
        $toArray = $this->botCommand->toArray();

        $this->assertEquals($toArray, $plainArray);
    }

    public function test_fluentInterface(): void
    {
        $result = $this->botCommand
            ->setBot($this->bot)
            ->setCommand('help')
            ->setHandler('App\\Handler\\HelpCommandHandler')
            ->setDescription('Show help')
            ->setValid(true);

        $this->assertSame($this->botCommand, $result);
        $this->assertEquals('help', $this->botCommand->getCommand());
        $this->assertEquals('App\\Handler\\HelpCommandHandler', $this->botCommand->getHandler());
        $this->assertEquals('Show help', $this->botCommand->getDescription());
        $this->assertTrue($this->botCommand->isValid());
    }
} 