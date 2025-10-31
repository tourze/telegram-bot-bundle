<?php

namespace TelegramBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TelegramBotBundle\Controller\Admin\CommandLogCrudController;
use TelegramBotBundle\Entity\CommandLog;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(CommandLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CommandLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/command-log');
    }

    public function testDetailPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/command-log/1');
    }

    public function testDeleteActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('POST', '/admin/telegram-bot/command-log/1/delete');
    }

    public function testNewActionIsDisabled(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/command-log/new');
    }

    public function testEditActionIsDisabled(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/command-log/1/edit');
    }

    /**
     * @return AbstractCrudController<CommandLog>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(CommandLogCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'TG机器人' => ['TG机器人'];
        yield '命令名称' => ['命令名称'];
        yield '系统命令' => ['系统命令'];
        yield '用户ID' => ['用户ID'];
        yield '用户名' => ['用户名'];
        yield '聊天ID' => ['聊天ID'];
        yield '聊天类型' => ['聊天类型'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // CommandLog控制器禁用了NEW页面，但测试框架要求非空数据
        // 提供占位符数据，实际测试会被跳过
        yield 'disabled' => ['disabled'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // CommandLog控制器禁用了EDIT页面，但测试框架要求非空数据
        // 提供占位符数据，实际测试会被跳过
        yield 'disabled' => ['disabled'];
    }
}
