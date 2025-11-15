<?php

namespace TelegramBotBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram Bot API 服务
 *
 * 参考文档: https://core.telegram.org/bots/api
 */
#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'telegram_bot')]
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
        if (($response['ok'] ?? false) === true) {
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

        return ($response['ok'] ?? false) === true;
    }

    /**
     * 发送 API 请求
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function makeRequest(TelegramBot $bot, string $method, array $params = []): array
    {
        $this->logger->info('TG机器人API调用接口 by 机器人', [
            'bot' => $bot,
            'method' => $method,
            'params' => $params,
        ]);

        try {
            return $this->makeHttpRequest($bot->getToken(), $method, $params);
        } catch (\Exception $e) {
            // 如果网络请求失败，返回错误响应格式
            $this->logger->error('TG机器人API请求失败', [
                'bot' => $bot,
                'method' => $method,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error_code' => 0, 'description' => $e->getMessage()];
        }
    }

    /**
     * 发送 API 请求
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function makeHttpRequest(string $token, string $method, array $params = []): array
    {
        $url = self::API_BASE_URL . $token . '/' . $method;
        $startTime = microtime(true);

        try {
            // 审计日志：记录请求开始
            $this->logger->info('TG机器人API请求开始', [
                'method' => $method,
                'url' => $url,
                'params' => $params,
                'request_start_time' => date('Y-m-d H:i:s'),
            ]);

            $response = $this->httpClient->request('POST', $url, [
                'json' => $params,
            ]);

            $responseContent = $response->getContent();
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            // 审计日志：记录响应结果
            $this->logger->info('TG机器人API调用成功', [
                'method' => $method,
                'params' => $params,
                'response_content' => $responseContent,
                'response_size' => strlen($responseContent),
                'duration_ms' => $duration,
                'status_code' => $response->getStatusCode(),
            ]);

            /** @var array<string, mixed> $decoded */
            $decoded = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);

            return $decoded;
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            // 审计日志：记录异常情况
            $this->logger->error('TG机器人API调用失败', [
                'method' => $method,
                'params' => $params,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
