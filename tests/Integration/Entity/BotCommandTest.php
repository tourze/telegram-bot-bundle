<?php

namespace TelegramBotBundle\Tests\Integration\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BotCommandTest extends KernelTestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}