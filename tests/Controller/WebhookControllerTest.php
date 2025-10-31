<?php

namespace TelegramBotBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use TelegramBotBundle\Controller\WebhookController;
use TelegramBotBundle\Entity\TelegramBot;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 *
 * 测试覆盖说明：该控制器仅支持 POST 方法处理 Webhook，其他 HTTP 方法返回 405 Method Not Allowed
 */
#[CoversClass(WebhookController::class)]
#[RunTestsInSeparateProcesses]
final class WebhookControllerTest extends AbstractWebTestCase
{
    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request($method, '/telegram/webhook/test-bot');
    }

    public function testPostWebhookWithInvalidBotReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $content = json_encode(['update_id' => 123, 'message' => ['text' => 'test']]);
        $this->assertNotFalse($content);

        $client->request('POST', '/telegram/webhook/invalid-bot-id', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testPostWebhookWithInvalidJsonReturnsBadRequest(): void
    {
        $client = self::createClientWithDatabase();

        // 创建一个测试用的机器人
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-bot-token');
        $bot->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($bot);
        $entityManager->flush();

        $client->request('POST', '/telegram/webhook/' . $bot->getId(), [], [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid-json'
        );

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
