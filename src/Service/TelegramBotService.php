<?php

namespace TelegramBotBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram Bot API 服务
 *
 * 参考文档: https://core.telegram.org/bots/api
 */
class TelegramBotService
{
    private const API_BASE_URL = 'https://api.telegram.org/bot';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 设置 Webhook
     *
     * @see https://core.telegram.org/bots/api#setwebhook
     */
    public function setWebhook(TelegramBot $bot, string $webhookUrl): bool
    {
        $response = $this->makeRequest($bot, 'setWebhook', [
            'url' => $webhookUrl,
            'allowed_updates' => ['message', 'callback_query'], // 只接收消息和回调查询
        ]);

        // {"ok":true,"result":true,"description":"Webhook is set"}
        if ($response['ok'] ?? false) {
            $bot->setWebhookUrl($webhookUrl);
            $this->entityManager->persist($bot);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * 发送消息
     *
     * @see https://core.telegram.org/bots/api#sendmessage
     */
    public function sendMessage(TelegramBot $bot, string $chatId, string $text): bool
    {
        $response = $this->makeRequest($bot, 'sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

        return $response['ok'] ?? false;
    }

    /**
     * 发送 API 请求
     */
    private function makeRequest(TelegramBot $bot, string $method, array $params = []): array
    {
        $this->logger->info('TG机器人API调用接口 by 机器人', [
            'bot' => $bot,
            'method' => $method,
            'params' => $params,
        ]);
        return $this->makeHttpRequest($bot->getToken(), $method, $params);
    }

    /**
     * 发送 API 请求
     */
    public function makeHttpRequest(string $token, string $method, array $params = []): array
    {
        $url = self::API_BASE_URL . $token . '/' . $method;

        $response = $this->httpClient->request('POST', $url, [
            'json' => $params,
        ]);

        $this->logger->info('TG机器人API调用接口', [
            'token' => $token,
            'method' => $method,
            'params' => $params,
            'response' => $response->getContent(),
        ]);

        return json_decode($response->getContent(), true);
    }
}
