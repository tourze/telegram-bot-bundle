<?php

namespace TelegramBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TelegramBotBundle\Controller\Admin\AutoReplyRuleCrudController;
use TelegramBotBundle\Entity\AutoReplyRule;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AutoReplyRuleCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AutoReplyRuleCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/auto-reply-rule');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/auto-reply-rule/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/auto-reply-rule/1/edit');
    }

    public function testDetailPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/telegram-bot/auto-reply-rule/1');
    }

    public function testDeleteActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('POST', '/admin/telegram-bot/auto-reply-rule/1/delete');
    }

    /**
     * @return AbstractCrudController<AutoReplyRule>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(AutoReplyRuleCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'TG机器人' => ['TG机器人'];
        yield '规则名称' => ['规则名称'];
        yield '匹配关键词' => ['匹配关键词'];
        yield '精确匹配' => ['精确匹配'];
        yield '优先级' => ['优先级'];
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
        yield 'name' => ['name'];
        yield 'keyword' => ['keyword'];
        yield 'replyContent' => ['replyContent'];
        yield 'exactMatch' => ['exactMatch'];
        yield 'priority' => ['priority'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'bot' => ['bot'];
        yield 'name' => ['name'];
        yield 'keyword' => ['keyword'];
        yield 'replyContent' => ['replyContent'];
        yield 'exactMatch' => ['exactMatch'];
        yield 'priority' => ['priority'];
        yield 'valid' => ['valid'];
    }
}
