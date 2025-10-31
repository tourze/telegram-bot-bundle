<?php

namespace TelegramBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TelegramBotBundle\Controller\Admin\TelegramUpdateCrudController;
use TelegramBotBundle\Entity\TelegramUpdate;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramUpdateCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TelegramUpdateCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-update');
    }

    public function testDetailPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-update/1');
    }

    public function testDeleteActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('POST', '/admin/telegram-bot/telegram-update/1/delete');
    }

    public function testNewActionIsDisabled(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-update/new');
    }

    public function testEditActionIsDisabled(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-update/1/edit');
    }

    /**
     * @return AbstractCrudController<TelegramUpdate>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(TelegramUpdateCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'TG机器人' => ['TG机器人'];
        yield '更新ID' => ['更新ID'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // TelegramUpdate控制器禁用了NEW页面，但测试框架要求非空数据
        // 提供占位符数据，实际测试会被跳过
        yield 'disabled' => ['disabled'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // TelegramUpdate控制器禁用了EDIT页面，但测试框架要求非空数据
        // 提供占位符数据，实际测试会被跳过
        yield 'disabled' => ['disabled'];
    }
}
