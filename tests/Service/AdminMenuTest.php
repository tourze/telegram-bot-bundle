<?php

namespace TelegramBotBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/list');

        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
    }

    public function testMenuCreation(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        // 使用真实的 MenuFactory 而不是 mock
        $factory = new MenuFactory();
        $rootItem = new MenuItem('root', $factory);

        $adminMenu($rootItem);

        $this->assertGreaterThan(0, count($rootItem->getChildren()), 'Root item should have at least one child');

        $telegramMenu = $rootItem->getChild('TG机器人');
        $this->assertNotNull($telegramMenu, 'TG机器人 menu should exist');
        $this->assertGreaterThan(0, count($telegramMenu->getChildren()), 'Telegram menu should have children');
        $this->assertNotNull($telegramMenu->getChild('机器人管理'));
        $this->assertNotNull($telegramMenu->getChild('命令管理'));
        $this->assertNotNull($telegramMenu->getChild('自动回复规则'));
        $this->assertNotNull($telegramMenu->getChild('命令日志'));
        $this->assertNotNull($telegramMenu->getChild('消息记录'));
    }
}
