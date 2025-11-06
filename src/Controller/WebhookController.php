<?php

namespace TelegramBotBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use TelegramBotBundle\Repository\TelegramBotRepository;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use TelegramBotBundle\Service\CommandParserService;

/**
 * 处理 Telegram Bot 的 Webhook 请求
 *
 * 参考文档: https://core.telegram.org/bots/api#getting-updates
 */
#[WithMonologChannel(channel: 'telegram_bot')]
final class WebhookController extends AbstractController
{
    public function __construct(
        private readonly TelegramBotRepository $botRepository,
        private readonly TelegramUpdateRepository $updateRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly CommandParserService $commandParser,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/telegram/webhook/{id}', name: 'telegram_bot_webhook', methods: ['POST'])]
    public function __invoke(string $id, Request $request): Response
    {
        $bot = $this->botRepository->findOneBy(['id' => $id, 'valid' => true]);
        if (null === $bot) {
            return new Response('Bot not found', Response::HTTP_NOT_FOUND);
        }

        $rawUpdate = $this->parseJsonRequest($request);
        if (null === $rawUpdate) {
            return new Response('Invalid request', Response::HTTP_BAD_REQUEST);
        }

        $updateId = $this->extractUpdateId($rawUpdate);
        if (null === $updateId) {
            return new Response('Missing update_id', Response::HTTP_BAD_REQUEST);
        }

        // 检查是否已经处理过这个更新
        $existingUpdate = $this->updateRepository->findByBotAndUpdateId($bot, $updateId);
        if (null !== $existingUpdate) {
            return new Response('OK');
        }

        // 创建和处理更新记录
        $update = $this->createUpdateRecord($bot, $updateId, $rawUpdate);
        $this->processMessages($update, $rawUpdate);

        // 保存更新记录
        $this->entityManager->persist($update);
        $this->entityManager->flush();

        $this->handleCommand($bot, $update);
        $this->dispatcher->dispatch(new TelegramUpdateEvent($bot, $update));

        return new Response('OK');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseJsonRequest(Request $request): ?array
    {
        $rawUpdate = json_decode($request->getContent(), true);
        if (!is_array($rawUpdate)) {
            return null;
        }

        $this->logger->info('收到TG WebHook回调', [
            'update' => $rawUpdate,
        ]);

        /** @var array<string, mixed> $rawUpdate */
        return $rawUpdate;
    }

    /**
     * @param array<string, mixed> $rawUpdate
     */
    private function extractUpdateId(array $rawUpdate): ?string
    {
        if (!isset($rawUpdate['update_id'])) {
            return null;
        }

        $updateIdValue = $rawUpdate['update_id'];
        return is_scalar($updateIdValue) ? (string) $updateIdValue : null;
    }

    /**
     * @param array<string, mixed> $rawUpdate
     */
    private function createUpdateRecord(TelegramBot $bot, string $updateId, array $rawUpdate): TelegramUpdate
    {
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId($updateId);
        $update->setRawData($rawUpdate);

        return $update;
    }

    /**
     * @param array<string, mixed> $rawUpdate
     */
    private function processMessages(TelegramUpdate $update, array $rawUpdate): void
    {
        // 处理消息
        if (isset($rawUpdate['message']) && is_array($rawUpdate['message'])) {
            /** @var array<string, mixed> $messageData */
            $messageData = $rawUpdate['message'];
            $update->setMessage($this->createMessage($messageData));
        }

        // 处理编辑的消息
        if (isset($rawUpdate['edited_message']) && is_array($rawUpdate['edited_message'])) {
            /** @var array<string, mixed> $messageData */
            $messageData = $rawUpdate['edited_message'];
            $update->setEditedMessage($this->createMessage($messageData));
        }

        // 处理频道消息
        if (isset($rawUpdate['channel_post']) && is_array($rawUpdate['channel_post'])) {
            /** @var array<string, mixed> $messageData */
            $messageData = $rawUpdate['channel_post'];
            $update->setChannelPost($this->createMessage($messageData));
        }

        // 处理编辑的频道消息
        if (isset($rawUpdate['edited_channel_post']) && is_array($rawUpdate['edited_channel_post'])) {
            /** @var array<string, mixed> $messageData */
            $messageData = $rawUpdate['edited_channel_post'];
            $update->setEditedChannelPost($this->createMessage($messageData));
        }
    }

    private function handleCommand(TelegramBot $bot, TelegramUpdate $update): void
    {
        $message = $update->getMessage();
        if (null !== $message) {
            $this->commandParser->parseAndDispatch($bot, $message);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createMessage(array $data): TelegramMessage
    {
        $message = new TelegramMessage();
        $this->setMessageBasicFields($message, $data);
        $this->setMessageFromUser($message, $data);
        $this->setMessageChat($message, $data);

        return $message;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setMessageBasicFields(TelegramMessage $message, array $data): void
    {
        $messageId = $data['message_id'] ?? null;
        $message->setMessageId(is_int($messageId) ? $messageId : null);

        $date = $data['date'] ?? null;
        $message->setDate(is_int($date) ? $date : null);

        $text = $data['text'] ?? null;
        $message->setText(is_string($text) ? $text : null);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setMessageFromUser(TelegramMessage $message, array $data): void
    {
        if (!isset($data['from']) || !is_array($data['from'])) {
            return;
        }

        $from = new TelegramUser();
        $fromData = $data['from'];

        $id = $fromData['id'] ?? null;
        $from->setId(is_int($id) ? $id : null);

        $isBot = $fromData['is_bot'] ?? null;
        $from->setIsBot(is_bool($isBot) ? $isBot : null);

        $firstName = $fromData['first_name'] ?? null;
        $from->setFirstName(is_string($firstName) ? $firstName : null);

        $lastName = $fromData['last_name'] ?? null;
        $from->setLastName(is_string($lastName) ? $lastName : null);

        $username = $fromData['username'] ?? null;
        $from->setUsername(is_string($username) ? $username : null);

        $languageCode = $fromData['language_code'] ?? null;
        $from->setLanguageCode(is_string($languageCode) ? $languageCode : null);

        $message->setFrom($from);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setMessageChat(TelegramMessage $message, array $data): void
    {
        if (!isset($data['chat']) || !is_array($data['chat'])) {
            return;
        }

        $chat = new TelegramChat();
        $chatData = $data['chat'];

        $id = $chatData['id'] ?? null;
        $chat->setId(is_int($id) ? $id : null);

        $type = $chatData['type'] ?? null;
        $chat->setType(is_string($type) ? $type : null);

        $title = $chatData['title'] ?? null;
        $chat->setTitle(is_string($title) ? $title : null);

        $username = $chatData['username'] ?? null;
        $chat->setUsername(is_string($username) ? $username : null);

        $firstName = $chatData['first_name'] ?? null;
        $chat->setFirstName(is_string($firstName) ? $firstName : null);

        $lastName = $chatData['last_name'] ?? null;
        $chat->setLastName(is_string($lastName) ? $lastName : null);

        $message->setChat($chat);
    }
}
