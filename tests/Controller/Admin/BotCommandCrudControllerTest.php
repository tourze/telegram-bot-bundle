<?php

namespace TelegramBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TelegramBotBundle\Controller\Admin\BotCommandCrudController;
use TelegramBotBundle\Entity\BotCommand;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(BotCommandCrudController::class)]
#[RunTestsInSeparateProcesses]
final class BotCommandCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/bot-command');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/bot-command/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/bot-command/1/edit');
    }

    public function testDetailPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/bot-command/1');
    }

    public function testDeleteActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('POST', '/admin/telegram-bot/bot-command/1/delete');
    }

    /**
     * @return AbstractCrudController<BotCommand>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(BotCommandCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'TG机器人' => ['TG机器人'];
        yield '命令名称' => ['命令名称'];
        yield '命令处理器类' => ['命令处理器类'];
        yield '命令描述' => ['命令描述'];
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
        yield 'bot' => ['bot'];
        yield 'command' => ['command'];
        yield 'handler' => ['handler'];
        yield 'description' => ['description'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'bot' => ['bot'];
        yield 'command' => ['command'];
        yield 'handler' => ['handler'];
        yield 'description' => ['description'];
        yield 'valid' => ['valid'];
    }
}
