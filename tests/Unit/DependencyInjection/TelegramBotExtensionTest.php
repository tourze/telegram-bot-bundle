<?php

namespace TelegramBotBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TelegramBotBundle\DependencyInjection\TelegramBotExtension;

class TelegramBotExtensionTest extends TestCase
{
    private TelegramBotExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new TelegramBotExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        // 简单测试
        $this->extension->load([], $this->container);

        // 验证一般配置已加载
        $this->assertNotEmpty($this->container->getDefinitions());
    }
}
