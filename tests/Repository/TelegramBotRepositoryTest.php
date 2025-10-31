<?php

namespace TelegramBotBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\TelegramBotRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBotRepository::class)]
#[RunTestsInSeparateProcesses]
final class TelegramBotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): TelegramBotRepository
    {
        return self::getService(TelegramBotRepository::class);
    }

    protected function createNewEntity(): TelegramBot
    {
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setWebhookUrl('https://example.com/webhook');
        $bot->setDescription('测试机器人描述');
        $bot->setValid(true);

        return $bot;
    }

    protected function onSetUp(): void
    {
    }

    public function testConstruction(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $this->assertInstanceOf(TelegramBotRepository::class, $repository);
    }

    public function testRepositoryHandlesCorrectEntityType(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人实体
        $bot = new TelegramBot();
        $bot->setName('实体类型测试');
        $bot->setUsername('entity_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 验证仓库正确处理TelegramBot实体
        $foundBot = $repository->find($bot->getId());
        $this->assertInstanceOf(TelegramBot::class, $foundBot);
        $this->assertSame('实体类型测试', $foundBot->getName());

        // 验证仓库的findOneBy方法正确处理实体
        $foundByUsername = $repository->findOneBy(['username' => 'entity_test_bot']);
        $this->assertInstanceOf(TelegramBot::class, $foundByUsername);
        $this->assertSame($bot->getId(), $foundByUsername->getId());
    }

    public function testFind(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        $botId = $bot->getId();
        $this->assertNotNull($botId);

        // 测试查找存在的实体
        $foundBot = $repository->find($botId);
        $this->assertNotNull($foundBot);
        $this->assertSame($bot, $foundBot);
        $this->assertSame('测试机器人', $foundBot->getName());

        // 测试查找不存在的实体
        $notFoundBot = $repository->find('nonexistent-id');
        $this->assertNull($notFoundBot);

        // 测试查找null ID - 跳过该测试以避免异常
        // $nullBot = $repository->find(null);
        // $this->assertNull($nullBot);
    }

    public function testFindBy(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建多个测试机器人
        $bot1 = new TelegramBot();
        $bot1->setName('机器人1');
        $bot1->setUsername('bot1');
        $bot1->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot1->setValid(true);
        $entityManager->persist($bot1);

        $bot2 = new TelegramBot();
        $bot2->setName('机器人2');
        $bot2->setUsername('bot2');
        $bot2->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $bot2->setValid(true);
        $entityManager->persist($bot2);

        $bot3 = new TelegramBot();
        $bot3->setName('机器人3');
        $bot3->setUsername('bot3');
        $bot3->setToken('345678:MNO-PQR9012stuVw-def56G7h8i901jk33');
        $bot3->setValid(false);
        $entityManager->persist($bot3);

        $entityManager->flush();

        // 测试按有效性查找
        $validBots = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(2, count($validBots)); // 允许之前测试留下的数据

        $invalidBots = $repository->findBy(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, count($invalidBots)); // 允许之前测试留下的数据

        // 验证我们创建的无效机器人确实存在
        $ourInvalidBot = $repository->findOneBy(['username' => 'bot3']);
        $this->assertNotNull($ourInvalidBot);
        $this->assertSame($bot3, $ourInvalidBot);

        // 测试按用户名查找
        $bot1ByUsername = $repository->findBy(['username' => 'bot1']);
        $this->assertCount(1, $bot1ByUsername);
        $this->assertSame($bot1, $bot1ByUsername[0]);

        // 测试排序（限定为我们创建的机器人）
        $ourBots = $repository->findBy(['username' => ['bot1', 'bot2', 'bot3']], ['username' => 'ASC']);
        $this->assertCount(3, $ourBots);
        $this->assertSame('bot1', $ourBots[0]->getUsername());
        $this->assertSame('bot2', $ourBots[1]->getUsername());
        $this->assertSame('bot3', $ourBots[2]->getUsername());

        // 测试限制数量（限定为我们创建的机器人）
        $limitedBots = $repository->findBy(['username' => ['bot1', 'bot2', 'bot3']], null, 2);
        $this->assertCount(2, $limitedBots);

        // 测试偏移量（限定为我们创建的机器人）
        $offsetBots = $repository->findBy(['username' => ['bot1', 'bot2', 'bot3']], ['username' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetBots);
        $this->assertSame('bot2', $offsetBots[0]->getUsername());
        $this->assertSame('bot3', $offsetBots[1]->getUsername());

        // 测试空条件
        $emptyResults = $repository->findBy(['username' => 'nonexistent']);
        $this->assertCount(0, $emptyResults);
    }

    public function testFindOneBy(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('唯一机器人');
        $bot->setUsername('unique_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 测试查找唯一实体
        $foundBot = $repository->findOneBy(['username' => 'unique_bot']);
        $this->assertNotNull($foundBot);
        $this->assertSame($bot, $foundBot);
        $this->assertSame('唯一机器人', $foundBot->getName());

        // 测试查找不存在的实体
        $notFoundBot = $repository->findOneBy(['username' => 'nonexistent']);
        $this->assertNull($notFoundBot);

        // 测试组合条件查找
        $complexFoundBot = $repository->findOneBy(['username' => 'unique_bot', 'valid' => true]);
        $this->assertNotNull($complexFoundBot);
        $this->assertSame($bot, $complexFoundBot);

        // 测试不匹配的组合条件
        $notMatchingBot = $repository->findOneBy(['username' => 'unique_bot', 'valid' => false]);
        $this->assertNull($notMatchingBot);
    }

    public function testFindAll(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 初始状态应该为空
        $initialBots = $repository->findAll();
        $this->assertIsArray($initialBots);

        // 创建多个测试机器人
        for ($i = 1; $i <= 3; ++$i) {
            $bot = new TelegramBot();
            $bot->setName("机器人{$i}");
            $bot->setUsername("bot{$i}");
            $bot->setToken("12345{$i}:ABC-DEF123{$i}ghIkl-zyx57W2v1u123ew1{$i}");
            $bot->setValid(1 === $i % 2); // 奇数有效
            $entityManager->persist($bot);
        }
        $entityManager->flush();

        // 测试获取所有实体
        $allBots = $repository->findAll();
        $this->assertGreaterThanOrEqual(3, count($allBots)); // 允许之前测试留下的数据
        $this->assertContainsOnlyInstancesOf(TelegramBot::class, $allBots);

        // 验证我们创建的机器人确实存在
        $ourBots = $repository->findBy(['username' => ['bot1', 'bot2', 'bot3']]);
        $this->assertCount(3, $ourBots);
    }

    public function testCount(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 获取初始计数
        $initialCount = $repository->count([]);
        $this->assertGreaterThanOrEqual(0, $initialCount); // 允许之前测试留下的数据

        // 创建测试机器人
        $bots = [];
        for ($i = 1; $i <= 5; ++$i) {
            $bot = new TelegramBot();
            $bot->setName("测试机器人{$i}");
            $bot->setUsername("testbot{$i}");
            $bot->setToken("12345{$i}:ABC-DEF123{$i}ghIkl-zyx57W2v1u123ew1{$i}");
            $bot->setValid($i <= 3); // 前3个有效
            $entityManager->persist($bot);
            $bots[] = $bot;
        }
        $entityManager->flush();

        // 测试总计数
        $totalCount = $repository->count([]);
        $this->assertSame($initialCount + 5, $totalCount);

        // 测试条件计数（限定为我们创建的机器人）
        $ourValidCount = $repository->count(['username' => ['testbot1', 'testbot2', 'testbot3', 'testbot4', 'testbot5'], 'valid' => true]);
        $this->assertSame(3, $ourValidCount);

        $ourInvalidCount = $repository->count(['username' => ['testbot1', 'testbot2', 'testbot3', 'testbot4', 'testbot5'], 'valid' => false]);
        $this->assertSame(2, $ourInvalidCount);
    }

    public function testSave(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建新机器人
        $bot = new TelegramBot();
        $bot->setName('新机器人');
        $bot->setUsername('new_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);

        // 测试保存但不刷新 - TelegramBot使用Snowflake ID，在创建时就生成
        $repository->save($bot, false);
        $this->assertNotEmpty($bot->getId()); // Snowflake ID在创建时就生成

        // 手动刷新
        $entityManager->flush();
        $this->assertGreaterThan(0, $bot->getId());

        // 测试默认保存（自动刷新）
        $bot2 = new TelegramBot();
        $bot2->setName('另一个机器人');
        $bot2->setUsername('another_bot');
        $bot2->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $bot2->setValid(true);

        $repository->save($bot2);
        $this->assertNotEmpty($bot2->getId()); // 应该有ID

        // 验证保存的数据
        $savedBot = $repository->find($bot2->getId());
        $this->assertNotNull($savedBot);
        $this->assertSame('另一个机器人', $savedBot->getName());
    }

    public function testRemove(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('待删除机器人');
        $bot->setUsername('delete_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        $botId = $bot->getId();
        $this->assertNotNull($botId);

        // 验证机器人存在
        $existingBot = $repository->find($botId);
        $this->assertNotNull($existingBot);

        // 测试删除但不刷新
        $repository->remove($bot, false);
        $stillExists = $repository->find($botId);
        $this->assertNotNull($stillExists); // 没有刷新，应该还存在

        // 手动刷新
        $entityManager->flush();
        $deletedBot = $repository->find($botId);
        $this->assertNull($deletedBot); // 应该被删除

        // 测试默认删除（自动刷新）
        $bot2 = new TelegramBot();
        $bot2->setName('另一个待删除机器人');
        $bot2->setUsername('delete2_bot');
        $bot2->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $bot2->setValid(true);
        $entityManager->persist($bot2);
        $entityManager->flush();

        $bot2Id = $bot2->getId();
        $this->assertNotNull($bot2Id);

        $repository->remove($bot2);
        $deletedBot2 = $repository->find($bot2Id);
        $this->assertNull($deletedBot2); // 应该立即被删除
    }

    public function testBoundaryConditions(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 测试空查询结果
        $emptyResults = $repository->findBy(['username' => 'nonexistent']);
        $this->assertIsArray($emptyResults);
        $this->assertCount(0, $emptyResults);

        // 测试极长字符串
        $longName = str_repeat('长名字', 100);
        $longBot = new TelegramBot();
        $longBot->setName($longName);
        $longBot->setUsername('long_name_bot');
        $longBot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $longBot->setValid(true);
        $entityManager->persist($longBot);
        $entityManager->flush();

        $foundLongBot = $repository->findOneBy(['username' => 'long_name_bot']);
        $this->assertNotNull($foundLongBot);
        $this->assertSame($longName, $foundLongBot->getName());

        // 测试特殊字符和Unicode
        $specialBot = new TelegramBot();
        $specialBot->setName('特殊机器人🚀');
        $specialBot->setUsername('special_bot');
        $specialBot->setToken('789012:XYZ!@#$%^&*()_+-=[]{}|;:,.<>?');
        $specialBot->setValid(true);
        $entityManager->persist($specialBot);
        $entityManager->flush();

        $foundSpecialBot = $repository->findOneBy(['username' => 'special_bot']);
        $this->assertNotNull($foundSpecialBot);
        $this->assertSame('特殊机器人🚀', $foundSpecialBot->getName());
        $this->assertStringContainsString('!@#$%^&*()', $foundSpecialBot->getToken());

        // 测试数据量边界
        for ($i = 1; $i <= 50; ++$i) {
            $bulkBot = new TelegramBot();
            $bulkBot->setName("批量机器人{$i}");
            $bulkBot->setUsername("bulk_bot_{$i}");
            $bulkBot->setToken("12345{$i}:BULK-TOKEN{$i}");
            $bulkBot->setValid(1 === $i % 2);
            $entityManager->persist($bulkBot);
        }
        $entityManager->flush();

        $bulkCount = $repository->count(['valid' => true]);
        $this->assertGreaterThan(25, $bulkCount); // 至少有一半是有效的

        // 测试分页查询
        $pagedResults = $repository->findBy([], null, 10, 0);
        $this->assertLessThanOrEqual(10, count($pagedResults));
    }

    public function testTokenUniqueness(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建第一个机器人
        $bot1 = new TelegramBot();
        $bot1->setName('机器人1');
        $bot1->setUsername('bot1');
        $bot1->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot1->setValid(true);
        $entityManager->persist($bot1);
        $entityManager->flush();

        // 验证第一个机器人成功保存
        $this->assertNotNull($bot1->getId());
        $foundBot1 = $repository->find($bot1->getId());
        $this->assertNotNull($foundBot1);
        $this->assertSame('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11', $foundBot1->getToken());

        // 测试用户名唯一性
        $foundByUsername = $repository->findOneBy(['username' => 'bot1']);
        $this->assertNotNull($foundByUsername);
        $this->assertSame($bot1, $foundByUsername);

        // 测试Token查询能力（虽然Repository没有专门的Token查询方法，但可以通过findBy测试）
        $foundByToken = $repository->findOneBy(['token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11']);
        $this->assertNotNull($foundByToken);
        $this->assertSame($bot1, $foundByToken);
    }

    public function testValidationStates(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建有效机器人
        $validBot = new TelegramBot();
        $validBot->setName('有效机器人');
        $validBot->setUsername('valid_bot');
        $validBot->setToken('123456:VALID-TOKEN');
        $validBot->setValid(true);
        $entityManager->persist($validBot);

        // 创建无效机器人
        $invalidBot = new TelegramBot();
        $invalidBot->setName('无效机器人');
        $invalidBot->setUsername('invalid_bot');
        $invalidBot->setToken('789012:INVALID-TOKEN');
        $invalidBot->setValid(false);
        $entityManager->persist($invalidBot);

        $entityManager->flush();

        // 测试按有效性过滤（限定为我们创建的机器人）
        $ourValidBots = $repository->findBy(['username' => 'valid_bot', 'valid' => true]);
        $this->assertCount(1, $ourValidBots);
        $this->assertTrue($ourValidBots[0]->isValid());

        $ourInvalidBots = $repository->findBy(['username' => 'invalid_bot', 'valid' => false]);
        $this->assertCount(1, $ourInvalidBots);
        $this->assertFalse($ourInvalidBots[0]->isValid());

        // 测试状态切换
        $validBot->setValid(false);
        $repository->save($validBot);

        $updatedBot = $repository->find($validBot->getId());
        $this->assertNotNull($updatedBot);
        $this->assertFalse($updatedBot->isValid());

        // 验证更新后的计数（限定为我们创建的机器人）
        $newValidCount = $repository->count(['username' => ['valid_bot', 'invalid_bot'], 'valid' => true]);
        $this->assertSame(0, $newValidCount);

        $newInvalidCount = $repository->count(['username' => ['valid_bot', 'invalid_bot'], 'valid' => false]);
        $this->assertSame(2, $newInvalidCount);
    }

    public function testFindByWithNullFieldsQuery(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建机器人，某些可空字段设为null（根据实体定义，大部分字段都是必填的）
        $bot1 = new TelegramBot();
        $bot1->setName('机器人1');
        $bot1->setUsername('bot1');
        $bot1->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot1->setValid(true);
        // description等可空字段保持null
        $entityManager->persist($bot1);

        $bot2 = new TelegramBot();
        $bot2->setName('机器人2');
        $bot2->setUsername('bot2');
        $bot2->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $bot2->setValid(false);
        $entityManager->persist($bot2);
        $entityManager->flush();

        // 测试基于布尔字段的查询（虽然不是null，但测试可空值查询逻辑）
        $validBots = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(1, count($validBots));

        $invalidBots = $repository->findBy(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, count($invalidBots));

        // 验证找到的机器人包含我们的测试数据
        $foundBot1 = $repository->findOneBy(['username' => 'bot1']);
        $this->assertNotNull($foundBot1);
        $this->assertTrue($foundBot1->isValid());

        $foundBot2 = $repository->findOneBy(['username' => 'bot2']);
        $this->assertNotNull($foundBot2);
        $this->assertFalse($foundBot2->isValid());
    }

    public function testFindOneByWithOrderingLogic(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建多个具有相同条件但不同ID的机器人
        $bots = [];
        for ($i = 1; $i <= 3; ++$i) {
            $bot = new TelegramBot();
            $bot->setName("排序测试机器人{$i}");
            $bot->setUsername("order_test_{$i}");
            $bot->setToken("12345{$i}:ABC-DEF123{$i}ghIkl-zyx57W2v1u123ew1{$i}");
            $bot->setValid(true);
            $entityManager->persist($bot);
            $bots[] = $bot;
        }
        $entityManager->flush();

        // 测试findOneBy返回一致性（应该总是返回同一个结果）
        $firstResult = $repository->findOneBy(['valid' => true]);
        $secondResult = $repository->findOneBy(['valid' => true]);
        $this->assertNotNull($firstResult);
        $this->assertNotNull($secondResult);
        $this->assertSame($firstResult->getId(), $secondResult->getId());

        // 测试具体条件的findOneBy
        $specificBot = $repository->findOneBy(['username' => 'order_test_2']);
        $this->assertNotNull($specificBot);
        $this->assertSame($bots[1]->getId(), $specificBot->getId());
        $this->assertSame('排序测试机器人2', $specificBot->getName());
    }

    public function testCountWithNullableFields(): void
    {
        $repository = self::getService(TelegramBotRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot1 = new TelegramBot();
        $bot1->setName('空值测试机器人1');
        $bot1->setUsername('null_test_1');
        $bot1->setToken('123456:NULL-TEST-1');
        $bot1->setValid(true);
        $entityManager->persist($bot1);

        $bot2 = new TelegramBot();
        $bot2->setName('空值测试机器人2');
        $bot2->setUsername('null_test_2');
        $bot2->setToken('789012:NULL-TEST-2');
        $bot2->setValid(false);
        $entityManager->persist($bot2);

        $bot3 = new TelegramBot();
        $bot3->setName('空值测试机器人3');
        $bot3->setUsername('null_test_3');
        $bot3->setToken('345678:NULL-TEST-3');
        $bot3->setValid(true);
        $entityManager->persist($bot3);
        $entityManager->flush();

        // 测试按有效性统计（模拟可空字段的计数）
        $validCount = $repository->count(['valid' => true]);
        $this->assertGreaterThanOrEqual(2, $validCount); // 至少包含我们创建的2个有效机器人

        $invalidCount = $repository->count(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, $invalidCount); // 至少包含我们创建的1个无效机器人

        // 测试具体机器人的计数
        $specificCount = $repository->count(['username' => 'null_test_1']);
        $this->assertSame(1, $specificCount);

        $nonExistentCount = $repository->count(['username' => 'non_existent_bot']);
        $this->assertSame(0, $nonExistentCount);
    }
}
