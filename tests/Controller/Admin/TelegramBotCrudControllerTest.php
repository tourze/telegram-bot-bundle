<?php

namespace TelegramBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TelegramBotBundle\Controller\Admin\TelegramBotCrudController;
use TelegramBotBundle\Entity\TelegramBot;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBotCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TelegramBotCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-bot');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-bot/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-bot/1/edit');
    }

    public function testDetailPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/telegram-bot/1');
    }

    public function testDeleteActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('POST', '/admin/telegram-bot/telegram-bot/1/delete');
    }

    /**
     * @return AbstractCrudController<TelegramBot>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(TelegramBotCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '机器人名称' => ['机器人名称'];
        yield '机器人用户名' => ['机器人用户名'];
        yield '机器人Token' => ['机器人Token'];
        yield 'Webhook URL' => ['Webhook URL'];
        yield '有效' => ['有效'];
        yield '创建人' => ['创建人'];
        yield '更新人' => ['更新人'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'username' => ['username'];
        yield 'token' => ['token'];
        yield 'webhookUrl' => ['webhookUrl'];
        yield 'description' => ['description'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'username' => ['username'];
        yield 'token' => ['token'];
        yield 'webhookUrl' => ['webhookUrl'];
        yield 'description' => ['description'];
        yield 'valid' => ['valid'];
    }
}
