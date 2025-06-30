<?php

namespace TelegramBotBundle\Tests\Integration\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AutoReplySubscriberTest extends KernelTestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}