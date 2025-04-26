<?php

namespace TelegramBotBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use TelegramBotBundle\Repository\TelegramBotRepository;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use TelegramBotBundle\Service\CommandParserService;
use Yiisoft\Json\Json;

/**
 * 处理 Telegram Bot 的 Webhook 请求
 *
 * 参考文档: https://core.telegram.org/bots/api#getting-updates
 */
class WebhookController extends AbstractController
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

    #[Route('/telegram/webhook/{id}', name: 'telegram_bot_webhook', methods: ['POST'])]
    public function webhook(string $id, Request $request): Response
    {
        $bot = $this->botRepository->findOneBy(['id' => $id, 'valid' => true]);
        if (!$bot) {
            return new Response('Bot not found', Response::HTTP_NOT_FOUND);
        }

        $rawUpdate = Json::decode($request->getContent());
        if (!$rawUpdate) {
            return new Response('Invalid request', Response::HTTP_BAD_REQUEST);
        }
        $this->logger->info('收到TG WebHook回调', [
            'bot' => $bot,
            'update' => $rawUpdate,
        ]);

        // 检查是否已经处理过这个更新
        $existingUpdate = $this->updateRepository->findByBotAndUpdateId($bot, (string) $rawUpdate['update_id']);
        if ($existingUpdate) {
            return new Response('OK');
        }

        // 创建更新记录
        $update = new TelegramUpdate();
        $update->setBot($bot)
            ->setUpdateId((string) $rawUpdate['update_id'])
            ->setRawData($rawUpdate);

        // 处理消息
        if (isset($rawUpdate['message'])) {
            $update->setMessage($this->createMessage($rawUpdate['message']));
        }

        // 处理编辑的消息
        if (isset($rawUpdate['edited_message'])) {
            $update->setEditedMessage($this->createMessage($rawUpdate['edited_message']));
        }

        // 处理频道消息
        if (isset($rawUpdate['channel_post'])) {
            $update->setChannelPost($this->createMessage($rawUpdate['channel_post']));
        }

        // 处理编辑的频道消息
        if (isset($rawUpdate['edited_channel_post'])) {
            $update->setEditedChannelPost($this->createMessage($rawUpdate['edited_channel_post']));
        }

        // 保存更新记录
        $this->entityManager->persist($update);
        $this->entityManager->flush();

        // 如果是消息，尝试解析命令
        if ($message = $update->getMessage()) {
            $this->commandParser->parseAndDispatch($bot, $message);
        }

        // 分发更新事件，让其他订阅者处理
        $this->dispatcher->dispatch(new TelegramUpdateEvent($bot, $update));

        return new Response('OK');
    }

    private function createMessage(array $data): TelegramMessage
    {
        $message = new TelegramMessage();
        $message->setMessageId($data['message_id'] ?? null)
            ->setDate($data['date'] ?? null)
            ->setText($data['text'] ?? null);

        if (isset($data['from'])) {
            $from = new TelegramUser();
            $from->setId($data['from']['id'] ?? null)
                ->setIsBot($data['from']['is_bot'] ?? null)
                ->setFirstName($data['from']['first_name'] ?? null)
                ->setLastName($data['from']['last_name'] ?? null)
                ->setUsername($data['from']['username'] ?? null)
                ->setLanguageCode($data['from']['language_code'] ?? null);
            $message->setFrom($from);
        }

        if (isset($data['chat'])) {
            $chat = new TelegramChat();
            $chat->setId($data['chat']['id'] ?? null)
                ->setType($data['chat']['type'] ?? null)
                ->setTitle($data['chat']['title'] ?? null)
                ->setUsername($data['chat']['username'] ?? null)
                ->setFirstName($data['chat']['first_name'] ?? null)
                ->setLastName($data['chat']['last_name'] ?? null);
            $message->setChat($chat);
        }

        return $message;
    }
}
