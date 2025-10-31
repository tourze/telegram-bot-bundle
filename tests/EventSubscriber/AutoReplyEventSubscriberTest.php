<?php

namespace TelegramBotBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Entity\Embeddable\TelegramChat;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Entity\Embeddable\TelegramUser;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use TelegramBotBundle\EventSubscriber\AutoReplyEventSubscriber;
use TelegramBotBundle\Repository\AutoReplyRuleRepository;
use TelegramBotBundle\Service\TelegramBotService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(AutoReplyEventSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class AutoReplyEventSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 设置默认的 mock 服务，测试方法可以覆盖
    }

    public function testGetSubscribedEvents(): void
    {
        $events = AutoReplyEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(TelegramUpdateEvent::class, $events);
        $this->assertEquals(['onTelegramUpdate', 0], $events[TelegramUpdateEvent::class]);
    }

    public function testOnTelegramUpdateWithEmptyMessageDoesNothing(): void
    {
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        // 使用反射设置ID，因为这是单元测试不涉及数据库
        $reflection = new \ReflectionClass($bot);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($bot, 1);

        // 使用具体类 TelegramBotService 进行 Mock：
        // 1) TelegramBotService 是具体的服务类，没有对应的接口定义
        // 2) 测试需要模拟其 sendMessage 方法的行为，确保在空消息时不会被调用
        // 3) 这种使用是合理的，因为该服务类职责单一且方法签名稳定
        // 4) 未来可考虑为该服务定义接口以提高可测试性
        $botService = $this->createMock(TelegramBotService::class);
        $botService->expects($this->never())->method('sendMessage');

        // 使用具体类 AutoReplyRuleRepository 进行 Mock：
        // 1) AutoReplyRuleRepository 是 Doctrine Repository 类，没有通用接口
        // 2) 测试需要模拟其 findMatchingRules 查询方法，确保在空消息时不会被调用
        // 3) Doctrine Repository 类通常不实现接口，直接 Mock 是标准做法
        // 4) 这种使用是必要且合理的，符合 Doctrine 生态的测试模式
        $ruleRepository = $this->createMock(AutoReplyRuleRepository::class);
        $ruleRepository->expects($this->never())->method('findMatchingRules');

        // 设置 mock 到容器
        self::getContainer()->set(TelegramBotService::class, $botService);
        self::getContainer()->set(AutoReplyRuleRepository::class, $ruleRepository);

        $testSubscriber = self::getService(AutoReplyEventSubscriber::class);

        // 创建完整的 TelegramUpdate 实体
        $update = $this->createTelegramUpdate($bot, '', 123);
        $event = new TelegramUpdateEvent($bot, $update);
        $testSubscriber->onTelegramUpdate($event);
    }

    public function testOnTelegramUpdateWithNoChatIdDoesNothing(): void
    {
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        // 使用反射设置ID，因为这是单元测试不涉及数据库
        $reflection = new \ReflectionClass($bot);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($bot, 1);

        // 使用具体类 TelegramBotService 进行 Mock：
        // 1) TelegramBotService 是具体的服务类，没有对应的接口定义
        // 2) 测试需要模拟其 sendMessage 方法的行为，确保在没有聊天ID时不会被调用
        // 3) 这种使用是合理的，因为该服务类职责单一且方法签名稳定
        // 4) 未来可考虑为该服务定义接口以提高可测试性
        $botService = $this->createMock(TelegramBotService::class);
        $botService->expects($this->never())->method('sendMessage');

        // 使用具体类 AutoReplyRuleRepository 进行 Mock：
        // 1) AutoReplyRuleRepository 是 Doctrine Repository 类，没有通用接口
        // 2) 测试需要模拟其 findMatchingRules 查询方法，确保在没有聊天ID时不会被调用
        // 3) Doctrine Repository 类通常不实现接口，直接 Mock 是标准做法
        // 4) 这种使用是必要且合理的，符合 Doctrine 生态的测试模式
        $ruleRepository = $this->createMock(AutoReplyRuleRepository::class);
        $ruleRepository->expects($this->never())->method('findMatchingRules');

        // 设置 mock 到容器
        self::getContainer()->set(TelegramBotService::class, $botService);
        self::getContainer()->set(AutoReplyRuleRepository::class, $ruleRepository);

        $testSubscriber = self::getService(AutoReplyEventSubscriber::class);

        // 创建没有聊天ID的 TelegramUpdate
        $update = $this->createTelegramUpdate($bot, 'hello', null);
        $event = new TelegramUpdateEvent($bot, $update);
        $testSubscriber->onTelegramUpdate($event);
    }

    public function testOnTelegramUpdateWithMatchingExactRuleSendsReply(): void
    {
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        // 使用反射设置ID，因为这是单元测试不涉及数据库
        $reflection = new \ReflectionClass($bot);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($bot, 1);

        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setKeyword('hello');
        $rule->setReplyContent('Hi there!');
        $rule->setExactMatch(true);
        $rule->setValid(true);

        // 使用具体类 AutoReplyRuleRepository 进行 Mock：
        // 1) AutoReplyRuleRepository 是 Doctrine Repository 类，没有通用接口
        // 2) 测试需要模拟其 findMatchingRules 查询方法，返回匹配的规则
        // 3) Doctrine Repository 类通常不实现接口，直接 Mock 是标准做法
        // 4) 这种使用是必要且合理的，符合 Doctrine 生态的测试模式
        $ruleRepository = $this->createMock(AutoReplyRuleRepository::class);
        $ruleRepository->expects($this->once())
            ->method('findMatchingRules')
            ->with(self::anything(), 'hello')
            ->willReturn([$rule])
        ;

        // 使用具体类 TelegramBotService 进行 Mock：
        // 1) TelegramBotService 是具体的服务类，没有对应的接口定义
        // 2) 测试需要模拟其 sendMessage 方法的行为，验证消息发送逻辑
        // 3) 这种使用是合理的，因为该服务类职责单一且方法签名稳定
        // 4) 未来可考虑为该服务定义接口以提高可测试性
        $botService = $this->createMock(TelegramBotService::class);
        $botService->expects($this->once())
            ->method('sendMessage')
            ->with($bot, '123', 'Hi there!')
        ;

        // 设置 mock 到容器
        self::getContainer()->set(TelegramBotService::class, $botService);
        self::getContainer()->set(AutoReplyRuleRepository::class, $ruleRepository);

        $testSubscriber = self::getService(AutoReplyEventSubscriber::class);

        $update = $this->createTelegramUpdate($bot, 'hello', 123);
        $event = new TelegramUpdateEvent($bot, $update);
        $testSubscriber->onTelegramUpdate($event);
    }

    public function testOnTelegramUpdateWithMatchingContainsRuleSendsReply(): void
    {
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        // 使用反射设置ID，因为这是单元测试不涉及数据库
        $reflection = new \ReflectionClass($bot);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($bot, 1);

        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setKeyword('help');
        $rule->setReplyContent('How can I help you?');
        $rule->setExactMatch(false);
        $rule->setValid(true);

        // 使用具体类 AutoReplyRuleRepository 进行 Mock：
        // 1) AutoReplyRuleRepository 是 Doctrine Repository 类，没有通用接口
        // 2) 测试需要模拟其 findMatchingRules 查询方法，返回匹配的规则
        // 3) Doctrine Repository 类通常不实现接口，直接 Mock 是标准做法
        // 4) 这种使用是必要且合理的，符合 Doctrine 生态的测试模式
        $ruleRepository = $this->createMock(AutoReplyRuleRepository::class);
        $ruleRepository->expects($this->once())
            ->method('findMatchingRules')
            ->with(self::anything(), 'I need help please')
            ->willReturn([$rule])
        ;

        // 使用具体类 TelegramBotService 进行 Mock：
        // 1) TelegramBotService 是具体的服务类，没有对应的接口定义
        // 2) 测试需要模拟其 sendMessage 方法的行为，验证消息发送逻辑
        // 3) 这种使用是合理的，因为该服务类职责单一且方法签名稳定
        // 4) 未来可考虑为该服务定义接口以提高可测试性
        $botService = $this->createMock(TelegramBotService::class);
        $botService->expects($this->once())
            ->method('sendMessage')
            ->with($bot, '123', 'How can I help you?')
        ;

        // 设置 mock 到容器
        self::getContainer()->set(TelegramBotService::class, $botService);
        self::getContainer()->set(AutoReplyRuleRepository::class, $ruleRepository);

        $testSubscriber = self::getService(AutoReplyEventSubscriber::class);

        $update = $this->createTelegramUpdate($bot, 'I need help please', 123);
        $event = new TelegramUpdateEvent($bot, $update);
        $testSubscriber->onTelegramUpdate($event);
    }

    public function testOnTelegramUpdateWithNoMatchingRulesDoesNotSendReply(): void
    {
        $bot = new TelegramBot();
        $bot->setName('Test Bot');
        $bot->setToken('test-token');
        // 使用反射设置ID，因为这是单元测试不涉及数据库
        $reflection = new \ReflectionClass($bot);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($bot, 1);

        // 使用具体类 AutoReplyRuleRepository 进行 Mock：
        // 1) AutoReplyRuleRepository 是 Doctrine Repository 类，没有通用接口
        // 2) 测试需要模拟其 findMatchingRules 查询方法，返回空数组表示没有匹配规则
        // 3) Doctrine Repository 类通常不实现接口，直接 Mock 是标准做法
        // 4) 这种使用是必要且合理的，符合 Doctrine 生态的测试模式
        $ruleRepository = $this->createMock(AutoReplyRuleRepository::class);
        $ruleRepository->expects($this->once())
            ->method('findMatchingRules')
            ->with(self::anything(), 'random message')
            ->willReturn([])
        ;

        // 使用具体类 TelegramBotService 进行 Mock：
        // 1) TelegramBotService 是具体的服务类，没有对应的接口定义
        // 2) 测试需要模拟其 sendMessage 方法的行为，确保在没有匹配规则时不会发送消息
        // 3) 这种使用是合理的，因为该服务类职责单一且方法签名稳定
        // 4) 未来可考虑为该服务定义接口以提高可测试性
        $botService = $this->createMock(TelegramBotService::class);
        $botService->expects($this->never())->method('sendMessage');

        // 设置 mock 到容器
        self::getContainer()->set(TelegramBotService::class, $botService);
        self::getContainer()->set(AutoReplyRuleRepository::class, $ruleRepository);

        $testSubscriber = self::getService(AutoReplyEventSubscriber::class);

        $update = $this->createTelegramUpdate($bot, 'random message', 123);
        $event = new TelegramUpdateEvent($bot, $update);
        $testSubscriber->onTelegramUpdate($event);
    }

    private function createTelegramUpdate(TelegramBot $bot, string $text, ?int $chatId): TelegramUpdate
    {
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('123456789');
        $update->setRawData(['message' => ['text' => $text, 'chat' => ['id' => $chatId]]]);

        if ('' !== $text || null !== $chatId) {
            $message = new TelegramMessage();
            $message->setText($text);
            $message->setMessageId(1001);
            $message->setDate(time());

            if (null !== $chatId) {
                $chat = new TelegramChat();
                $chat->setId($chatId);
                $chat->setType('private');
                $message->setChat($chat);
            }

            $user = new TelegramUser();
            $user->setId(123);
            $user->setFirstName('Test User');
            $user->setIsBot(false);
            $message->setFrom($user);

            $update->setMessage($message);
        }

        return $update;
    }
}
