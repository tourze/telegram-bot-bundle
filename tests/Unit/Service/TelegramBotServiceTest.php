<?php

namespace TelegramBotBundle\Tests\Unit\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Service\TelegramBotService;

class TelegramBotServiceTest extends TestCase
{
    private TelegramBotService $telegramBotService;
    private MockObject|HttpClientInterface $httpClient;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|LoggerInterface $logger;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->telegramBotService = new TelegramBotService(
            $this->httpClient,
            $this->entityManager,
            $this->logger
        );

        $this->bot = new TelegramBot();
        $this->bot->setToken('test_token');
    }

    public function testServiceInstantiation(): void
    {
        // 测试服务实例化
        $this->assertInstanceOf(TelegramBotService::class, $this->telegramBotService);
    }
}
