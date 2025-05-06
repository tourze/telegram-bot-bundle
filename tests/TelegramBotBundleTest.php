<?php

namespace TelegramBotBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use TelegramBotBundle\TelegramBotBundle;

class TelegramBotBundleTest extends TestCase
{
    public function testBundleClassImplementsBundleInterface(): void
    {
        $bundle = new TelegramBotBundle();
        $this->assertInstanceOf(BundleInterface::class, $bundle);
    }

    public function testBuildContainer(): void
    {
        $bundle = new TelegramBotBundle();
        $container = new ContainerBuilder();

        // 没有实现特殊的build方法，但调用不应抛出异常
        $bundle->build($container);

        $this->assertTrue(true);
    }
}
