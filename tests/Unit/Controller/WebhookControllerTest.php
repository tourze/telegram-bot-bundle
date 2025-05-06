<?php

namespace TelegramBotBundle\Tests\Unit\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use TelegramBotBundle\Controller\WebhookController;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Repository\TelegramBotRepository;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use TelegramBotBundle\Service\CommandParserService;

class WebhookControllerTest extends TestCase
{
    private WebhookController $webhookController;
    private MockObject|TelegramBotRepository $botRepository;
    private MockObject|TelegramUpdateRepository $updateRepository;
    private MockObject|EventDispatcherInterface $dispatcher;
    private MockObject|CommandParserService $commandParser;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->botRepository = $this->createMock(TelegramBotRepository::class);
        $this->updateRepository = $this->createMock(TelegramUpdateRepository::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->commandParser = $this->createMock(CommandParserService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->webhookController = new WebhookController(
            $this->botRepository,
            $this->updateRepository,
            $this->dispatcher,
            $this->commandParser,
            $this->entityManager,
            $this->logger
        );
    }

    public function testWebhook_withValidMessageUpdate(): void
    {
        $botId = '123';
        $updateId = '456';
        $bot = new TelegramBot();
        $bot->setValid(true);

        $updateData = [
            'update_id' => $updateId,
            'message' => [
                'message_id' => 789,
                'date' => 1609459200,
                'text' => 'Hello, world!',
                'from' => [
                    'id' => 123456,
                    'is_bot' => false,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'username' => 'johndoe',
                    'language_code' => 'en'
                ],
                'chat' => [
                    'id' => 123456,
                    'type' => 'private',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'username' => 'johndoe'
                ]
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $botId, 'valid' => true])
            ->willReturn($bot);

        $this->updateRepository->expects($this->once())
            ->method('findByBotAndUpdateId')
            ->with($bot, $updateId)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($update) use ($bot, $updateId, $updateData) {
                return $update instanceof TelegramUpdate
                    && $update->getBot() === $bot
                    && $update->getUpdateId() === $updateId
                    && $update->getRawData() === $updateData
                    && $update->getMessage() instanceof TelegramMessage
                    && $update->getMessage()->getText() === 'Hello, world!';
            }));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->commandParser->expects($this->once())
            ->method('parseAndDispatch')
            ->with($bot, $this->isInstanceOf(TelegramMessage::class));

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function testWebhook_withInvalidBotId(): void
    {
        $botId = 'invalid';
        $request = new Request();

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $botId, 'valid' => true])
            ->willReturn(null);

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Bot not found', $response->getContent());
    }

    public function testWebhook_withInvalidRequestBody(): void
    {
        $botId = '123';
        $bot = new TelegramBot();
        $bot->setValid(true);

        $request = new Request([], [], [], [], [], [], 'invalid json');

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $botId, 'valid' => true])
            ->willReturn($bot);

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Invalid request', $response->getContent());
    }

    public function testWebhook_withAlreadyProcessedUpdate(): void
    {
        $botId = '123';
        $updateId = '456';
        $bot = new TelegramBot();
        $bot->setValid(true);

        $updateData = [
            'update_id' => $updateId,
            'message' => [
                'message_id' => 789,
                'date' => 1609459200,
                'text' => 'Hello, world!'
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($updateData));
        $existingUpdate = new TelegramUpdate();

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $botId, 'valid' => true])
            ->willReturn($bot);

        $this->updateRepository->expects($this->once())
            ->method('findByBotAndUpdateId')
            ->with($bot, $updateId)
            ->willReturn($existingUpdate);

        // 已经处理过的更新不应该再次处理
        $this->entityManager->expects($this->never())
            ->method('persist');

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function testWebhook_withMinimalMessageData(): void
    {
        $botId = '123';
        $updateId = '456';
        $bot = new TelegramBot();
        $bot->setValid(true);

        // 极简的消息数据，只有必要字段
        $updateData = [
            'update_id' => $updateId,
            'message' => [
                'message_id' => 789
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($bot);

        $this->updateRepository->expects($this->once())
            ->method('findByBotAndUpdateId')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWebhook_withEditedMessageUpdate(): void
    {
        $botId = '123';
        $updateId = '456';
        $bot = new TelegramBot();
        $bot->setValid(true);

        $updateData = [
            'update_id' => $updateId,
            'edited_message' => [
                'message_id' => 789,
                'date' => 1609459200,
                'text' => 'Edited message'
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        $this->botRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($bot);

        $this->updateRepository->expects($this->once())
            ->method('findByBotAndUpdateId')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($update) {
                return $update instanceof TelegramUpdate
                    && $update->getEditedMessage() instanceof TelegramMessage
                    && $update->getEditedMessage()->getText() === 'Edited message';
            }));

        $response = $this->webhookController->webhook($botId, $request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
