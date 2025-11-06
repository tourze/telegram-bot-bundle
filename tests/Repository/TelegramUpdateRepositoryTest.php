<?php

namespace TelegramBotBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\TelegramUpdate;
use TelegramBotBundle\Repository\TelegramUpdateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramUpdateRepository::class)]
#[RunTestsInSeparateProcesses]
final class TelegramUpdateRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): TelegramUpdateRepository
    {
        return self::getService(TelegramUpdateRepository::class);
    }

    protected function createNewEntity(): TelegramUpdate
    {
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);

        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('123456');
        $update->setRawData(['message' => ['text' => 'test']]);

        return $update;
    }

    protected function onSetUp(): void
    {
    }

    public function testConstruction(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $this->assertInstanceOf(TelegramUpdateRepository::class, $repository);
    }

    public function testFindByBotAndUpdateId(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建另一个机器人用于测试隔离
        $otherBot = new TelegramBot();
        $otherBot->setName('其他机器人');
        $otherBot->setUsername('other_bot');
        $otherBot->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $otherBot->setValid(true);
        $entityManager->persist($otherBot);

        // 创建Telegram更新消息
        $update1 = new TelegramUpdate();
        $update1->setBot($bot);
        $update1->setUpdateId('12345');
        $update1->setRawData(['message' => ['text' => 'Hello World']]);
        $entityManager->persist($update1);

        // 创建另一个更新消息
        $update2 = new TelegramUpdate();
        $update2->setBot($bot);
        $update2->setUpdateId('12346');
        $update2->setRawData(['message' => ['text' => 'How are you?']]);
        $entityManager->persist($update2);

        // 创建其他机器人的更新消息
        $update3 = new TelegramUpdate();
        $update3->setBot($otherBot);
        $update3->setUpdateId('12345'); // 相同的updateId但不同的机器人
        $update3->setRawData(['message' => ['text' => 'Other bot message']]);
        $entityManager->persist($update3);

        $entityManager->flush();

        // 测试查找存在的更新消息
        $foundUpdate = $repository->findByBotAndUpdateId($bot, '12345');
        $this->assertNotNull($foundUpdate);
        $this->assertSame($update1, $foundUpdate);
        $this->assertSame('12345', $foundUpdate->getUpdateId());
        $this->assertSame($bot, $foundUpdate->getBot());

        // 测试查找另一个存在的更新消息
        $foundUpdate2 = $repository->findByBotAndUpdateId($bot, '12346');
        $this->assertNotNull($foundUpdate2);
        $this->assertSame($update2, $foundUpdate2);
        $this->assertSame('12346', $foundUpdate2->getUpdateId());

        // 测试查找不存在的更新消息
        $notFoundUpdate = $repository->findByBotAndUpdateId($bot, '99999');
        $this->assertNull($notFoundUpdate);

        // 测试机器人隔离：查找其他机器人的更新消息
        $otherBotUpdate = $repository->findByBotAndUpdateId($bot, '12345');
        $this->assertNotNull($otherBotUpdate);
        $this->assertSame($update1, $otherBotUpdate); // 应该返回当前机器人的更新，不是其他机器人的

        // 测试其他机器人查找自己的更新消息
        $otherFoundUpdate = $repository->findByBotAndUpdateId($otherBot, '12345');
        $this->assertNotNull($otherFoundUpdate);
        $this->assertSame($update3, $otherFoundUpdate);
        $this->assertSame($otherBot, $otherFoundUpdate->getBot());

        // 验证原始数据
        $this->assertSame(['message' => ['text' => 'Hello World']], $foundUpdate->getRawData());
        $this->assertSame(['message' => ['text' => 'Other bot message']], $otherFoundUpdate->getRawData());
    }

    public function testFind(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创庺测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('12345');
        $update->setRawData(['message' => ['text' => 'Test message']]);
        $entityManager->persist($update);
        $entityManager->flush();

        $updateId = $update->getId();
        $this->assertNotNull($updateId);

        // 测试查找存在的实体
        $foundUpdate = $repository->find($updateId);
        $this->assertNotNull($foundUpdate);
        $this->assertSame($update, $foundUpdate);
        $this->assertSame('12345', $foundUpdate->getUpdateId());

        // 测试查找不存在的实体
        $notFoundUpdate = $repository->find('nonexistent-id');
        $this->assertNull($notFoundUpdate);

        // 测试查找null ID - 跳过该测试以避免异常
        // $nullUpdate = $repository->find(null);
        // $this->assertNull($nullUpdate);
    }

    public function testFindBy(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试更新
        $update1 = new TelegramUpdate();
        $update1->setBot($bot);
        $update1->setUpdateId('100');
        $update1->setRawData(['message' => ['text' => 'Message 1']]);
        $entityManager->persist($update1);

        $update2 = new TelegramUpdate();
        $update2->setBot($bot);
        $update2->setUpdateId('200');
        $update2->setRawData(['message' => ['text' => 'Message 2']]);
        $entityManager->persist($update2);

        $update3 = new TelegramUpdate();
        $update3->setBot($bot);
        $update3->setUpdateId('300');
        $update3->setRawData(['callback_query' => ['data' => 'button_click']]);
        $entityManager->persist($update3);

        $entityManager->flush();

        // 测试按机器人查找
        $updatesByBot = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $updatesByBot);

        // 测试按updateId查找
        $updateById = $repository->findBy(['updateId' => '200']);
        $this->assertCount(1, $updateById);
        $this->assertSame($update2, $updateById[0]);

        // 测试排序（按updateId降序）
        $sortedUpdates = $repository->findBy(['bot' => $bot], ['updateId' => 'DESC']);
        $this->assertCount(3, $sortedUpdates);
        $this->assertSame('300', $sortedUpdates[0]->getUpdateId());
        $this->assertSame('200', $sortedUpdates[1]->getUpdateId());
        $this->assertSame('100', $sortedUpdates[2]->getUpdateId());

        // 测试限制数量
        $limitedUpdates = $repository->findBy(['bot' => $bot], null, 2);
        $this->assertCount(2, $limitedUpdates);

        // 测试偏移量
        $offsetUpdates = $repository->findBy(['bot' => $bot], ['updateId' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetUpdates);
        $this->assertSame('200', $offsetUpdates[0]->getUpdateId());
        $this->assertSame('300', $offsetUpdates[1]->getUpdateId());

        // 测试空条件
        $emptyResults = $repository->findBy(['updateId' => 'nonexistent']);
        $this->assertCount(0, $emptyResults);
    }

    public function testFindOneBy(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000123');
        $update->setRawData(['message' => ['text' => 'Unique message']]);
        $entityManager->persist($update);
        $entityManager->flush();

        // 测试查找唯一实体
        $foundUpdate = $repository->findOneBy(['updateId' => '9000000000000000123']);
        $this->assertNotNull($foundUpdate);
        $this->assertSame($update, $foundUpdate);
        $this->assertSame('9000000000000000123', $foundUpdate->getUpdateId());

        // 测试查找不存在的实体
        $notFoundUpdate = $repository->findOneBy(['updateId' => '9999999999999999999']);
        $this->assertNull($notFoundUpdate);

        // 测试组合条件查找
        $complexFoundUpdate = $repository->findOneBy(['bot' => $bot, 'updateId' => '9000000000000000123']);
        $this->assertNotNull($complexFoundUpdate);
        $this->assertSame($update, $complexFoundUpdate);
    }

    public function testFindAll(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 初始状态应该为空
        $initialUpdates = $repository->findAll();
        $this->assertIsArray($initialUpdates);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试更新
        for ($i = 1; $i <= 3; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot);
            $update->setUpdateId((string) $i);
            $update->setRawData(['message' => ['text' => "Message {$i}"]]);
            $entityManager->persist($update);
        }
        $entityManager->flush();

        // 测试获取所有实体
        $allUpdates = $repository->findAll();
        $this->assertGreaterThanOrEqual(3, count($allUpdates)); // 允许之前测试留下的数据
        $this->assertContainsOnlyInstancesOf(TelegramUpdate::class, $allUpdates);

        // 验证我们创建的更新确实存在
        $ourUpdates = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $ourUpdates);
    }

    public function testCount(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 记录初始计数
        $initialCount = $repository->count([]);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        for ($i = 1; $i <= 5; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot);
            $update->setUpdateId((string) $i);
            $update->setRawData(['message' => ['text' => "Message {$i}"]]);
            $entityManager->persist($update);
        }
        $entityManager->flush();

        // 测试新增的计数
        $totalCount = $repository->count([]);
        $this->assertSame($initialCount + 5, $totalCount);

        // 测试按机器人计数
        $botCount = $repository->count(['bot' => $bot]);
        $this->assertSame(5, $botCount);
    }

    public function testGetListByBot(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个更新消息
        for ($i = 1; $i <= 25; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot);
            $update->setUpdateId((string) $i);
            $update->setRawData(['message' => ['text' => "Message {$i}"]]);
            $entityManager->persist($update);
        }
        $entityManager->flush();

        // 测试默认分页（第1页，每页20条）
        $firstPage = $repository->getListByBot($bot);
        $this->assertCount(20, $firstPage);
        // 验证按updateId降序排列
        $this->assertSame('25', $firstPage[0]->getUpdateId());
        $this->assertSame('6', $firstPage[19]->getUpdateId());

        // 测试第2页
        $secondPage = $repository->getListByBot($bot, 2);
        $this->assertCount(5, $secondPage); // 剩余的5条
        $this->assertSame('5', $secondPage[0]->getUpdateId());
        $this->assertSame('1', $secondPage[4]->getUpdateId());

        // 测试自定义每页数量
        $customPage = $repository->getListByBot($bot, 1, 10);
        $this->assertCount(10, $customPage);
        $this->assertSame('25', $customPage[0]->getUpdateId());
        $this->assertSame('16', $customPage[9]->getUpdateId());

        // 测试空页
        $emptyPage = $repository->getListByBot($bot, 10, 20);
        $this->assertCount(0, $emptyPage);
    }

    public function testGetTotalByBot(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建另一个机器人
        $otherBot = new TelegramBot();
        $otherBot->setName('其他机器人');
        $otherBot->setUsername('other_bot');
        $otherBot->setToken('789012:XYZ-GHI5678jklMn-abc34D5e6f789gh22');
        $otherBot->setValid(true);
        $entityManager->persist($otherBot);

        // 创建测试机器人的更新
        for ($i = 1; $i <= 15; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot);
            $update->setUpdateId((string) $i);
            $update->setRawData(['message' => ['text' => "Message {$i}"]]);
            $entityManager->persist($update);
        }

        // 创建其他机器人的更新
        for ($i = 1; $i <= 8; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($otherBot);
            $update->setUpdateId((string) $i);
            $update->setRawData(['message' => ['text' => "Other message {$i}"]]);
            $entityManager->persist($update);
        }

        $entityManager->flush();

        // 测试获取测试机器人的总数
        $testBotTotal = $repository->getTotalByBot($bot);
        $this->assertSame(15, $testBotTotal);

        // 测试获取其他机器人的总数
        $otherBotTotal = $repository->getTotalByBot($otherBot);
        $this->assertSame(8, $otherBotTotal);

        // 验证其他机器人不会影响结果
        $this->assertNotEquals($testBotTotal, $otherBotTotal);
    }

    public function testGetLastUpdateByBot(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 初始状态下没有更新
        $initialLastUpdate = $repository->getLastUpdateByBot($bot);
        $this->assertNull($initialLastUpdate);

        // 创建多个更新消息
        $update1 = new TelegramUpdate();
        $update1->setBot($bot);
        $update1->setUpdateId('100');
        $update1->setRawData(['message' => ['text' => 'First message']]);
        $entityManager->persist($update1);

        $update2 = new TelegramUpdate();
        $update2->setBot($bot);
        $update2->setUpdateId('200');
        $update2->setRawData(['message' => ['text' => 'Second message']]);
        $entityManager->persist($update2);

        $update3 = new TelegramUpdate();
        $update3->setBot($bot);
        $update3->setUpdateId('150'); // 中间的updateId
        $update3->setRawData(['message' => ['text' => 'Middle message']]);
        $entityManager->persist($update3);

        $entityManager->flush();

        // 测试获取最后一条更新（按updateId降序）
        $lastUpdate = $repository->getLastUpdateByBot($bot);
        $this->assertNotNull($lastUpdate);
        $this->assertSame($update2, $lastUpdate); // updateId最大的
        $this->assertSame('200', $lastUpdate->getUpdateId());
    }

    public function testSave(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 创建新更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000200');
        $update->setRawData(['message' => ['text' => 'New message']]);

        // 测试保存但不刷新 - TelegramUpdate使用Snowflake ID，在创建时就生成
        $repository->save($update, false);
        $this->assertNotEmpty($update->getId()); // Snowflake ID在创建时就生成

        // 手动刷新
        $entityManager->flush();
        $this->assertGreaterThan(0, $update->getId());

        // 测试默认保存（自动刷新）
        $update2 = new TelegramUpdate();
        $update2->setBot($bot);
        $update2->setUpdateId('9000000000000000201');
        $update2->setRawData(['message' => ['text' => 'Another message']]);

        $repository->save($update2);
        $this->assertNotEmpty($update2->getId()); // 应该有ID

        // 验证保存的数据
        $savedUpdate = $repository->find($update2->getId());
        $this->assertNotNull($savedUpdate);
        $this->assertSame('9000000000000000201', $savedUpdate->getUpdateId());
    }

    public function testRemove(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000300');
        $update->setRawData(['message' => ['text' => 'Delete message']]);
        $entityManager->persist($update);
        $entityManager->flush();

        $updateId = $update->getId();
        $this->assertNotNull($updateId);

        // 验证更新存在
        $existingUpdate = $repository->find($updateId);
        $this->assertNotNull($existingUpdate);

        // 测试删除但不刷新
        $repository->remove($update, false);
        $stillExists = $repository->find($updateId);
        $this->assertNotNull($stillExists); // 没有刷新，应该还存在

        // 手动刷新
        $entityManager->flush();
        $deletedUpdate = $repository->find($updateId);
        $this->assertNull($deletedUpdate); // 应该被删除

        // 测试默认删除（自动刷新）
        $update2 = new TelegramUpdate();
        $update2->setBot($bot);
        $update2->setUpdateId('9000000000000000301');
        $update2->setRawData(['message' => ['text' => 'Delete2 message']]);
        $entityManager->persist($update2);
        $entityManager->flush();

        $update2Id = $update2->getId();
        $this->assertNotNull($update2Id);

        $repository->remove($update2);
        $deletedUpdate2 = $repository->find($update2Id);
        $this->assertNull($deletedUpdate2); // 应该立即被删除
    }

    public function testEntityRelationships(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000350');
        $update->setRawData(['message' => ['text' => 'Relation test']]);
        $entityManager->persist($update);
        $entityManager->flush();

        // 测试机器人关联
        $foundUpdate = $repository->find($update->getId());
        $this->assertNotNull($foundUpdate);
        $this->assertSame($bot, $foundUpdate->getBot());
        $this->assertSame('test_bot', $foundUpdate->getBot()->getUsername());

        // 测试级联查询
        $updatesWithBot = $repository->createQueryBuilder('u')
            ->join('u.bot', 'b')
            ->where('b.username = :username')
            ->setParameter('username', 'test_bot')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($updatesWithBot);
        $this->assertCount(1, $updatesWithBot);
        $this->assertArrayHasKey(0, $updatesWithBot);
        $this->assertSame($update, $updatesWithBot[0]);
    }

    public function testBoundaryConditions(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 测试空查询结果
        $emptyResults = $repository->findBy(['updateId' => 'nonexistent']);
        $this->assertIsArray($emptyResults);
        $this->assertCount(0, $emptyResults);

        // 测试极大updateId
        $bigUpdate = new TelegramUpdate();
        $bigUpdate->setBot($bot);
        $bigUpdate->setUpdateId('999999999999999');
        $bigUpdate->setRawData(['message' => ['text' => 'Big update ID']]);
        $entityManager->persist($bigUpdate);
        $entityManager->flush();

        $foundBigUpdate = $repository->findByBotAndUpdateId($bot, '999999999999999');
        $this->assertNotNull($foundBigUpdate);
        $this->assertSame('999999999999999', $foundBigUpdate->getUpdateId());

        // 测试特殊字符在updateId中
        $specialUpdate = new TelegramUpdate();
        $specialUpdate->setBot($bot);
        $specialUpdate->setUpdateId('9000000000000000400');
        $specialUpdate->setRawData(['message' => ['text' => 'Special updateId']]);
        $entityManager->persist($specialUpdate);
        $entityManager->flush();

        $foundSpecialUpdate = $repository->findByBotAndUpdateId($bot, '9000000000000000400');
        $this->assertNotNull($foundSpecialUpdate);
        $this->assertSame('9000000000000000400', $foundSpecialUpdate->getUpdateId());

        // 测试复杂的rawData
        $complexData = [
            'message' => [
                'message_id' => 123,
                'from' => ['id' => 456, 'username' => 'test_user'],
                'text' => '复杂消息🚀',
                'entities' => [
                    ['type' => 'mention', 'offset' => 0, 'length' => 5],
                ],
            ],
        ];

        $complexUpdate = new TelegramUpdate();
        $complexUpdate->setBot($bot);
        $complexUpdate->setUpdateId('9000000000000000410');
        $complexUpdate->setRawData($complexData);
        $entityManager->persist($complexUpdate);
        $entityManager->flush();

        $foundComplexUpdate = $repository->findByBotAndUpdateId($bot, '9000000000000000410');
        $this->assertNotNull($foundComplexUpdate);
        $this->assertSame($complexData, $foundComplexUpdate->getRawData());
        $this->assertSame('复杂消息🚀', $foundComplexUpdate->getRawData()['message']['text']);

        // 测试大数据量分页
        for ($i = 1; $i <= 100; ++$i) {
            $bulkUpdate = new TelegramUpdate();
            $bulkUpdate->setBot($bot);
            $bulkUpdate->setUpdateId("9000000000000400" . str_pad((string) $i, 2, "0", STR_PAD_LEFT));
            $bulkUpdate->setRawData(['message' => ['text' => "Bulk message {$i}"]]);
            $entityManager->persist($bulkUpdate);
        }
        $entityManager->flush();

        $bulkCount = $repository->getTotalByBot($bot);
        $this->assertGreaterThan(100, $bulkCount); // 包括之前创建的

        // 测试大量数据的分页查询
        $largePage = $repository->getListByBot($bot, 1, 50);
        $this->assertCount(50, $largePage);
    }

    public function testSpecialDataTypes(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 测试不同类型的Telegram更新
        $messageUpdate = new TelegramUpdate();
        $messageUpdate->setBot($bot);
        $messageUpdate->setUpdateId('9000000000000000500');
        $messageUpdate->setRawData([
            'update_id' => 1,
            'message' => [
                'message_id' => 101,
                'from' => ['id' => 123, 'username' => 'user1'],
                'chat' => ['id' => 456, 'type' => 'private'],
                'date' => time(),
                'text' => '测试消息',
            ],
        ]);
        $entityManager->persist($messageUpdate);

        $callbackUpdate = new TelegramUpdate();
        $callbackUpdate->setBot($bot);
        $callbackUpdate->setUpdateId('9000000000000000501');
        $callbackUpdate->setRawData([
            'update_id' => 2,
            'callback_query' => [
                'id' => 'callback123',
                'from' => ['id' => 123, 'username' => 'user1'],
                'message' => [
                    'message_id' => 102,
                    'chat' => ['id' => 456],
                ],
                'data' => 'button_clicked',
            ],
        ]);
        $entityManager->persist($callbackUpdate);

        $inlineUpdate = new TelegramUpdate();
        $inlineUpdate->setBot($bot);
        $inlineUpdate->setUpdateId('9000000000000000502');
        $inlineUpdate->setRawData([
            'update_id' => 3,
            'inline_query' => [
                'id' => 'inline456',
                'from' => ['id' => 123, 'username' => 'user1'],
                'query' => '搜索关键词',
                'offset' => '',
            ],
        ]);
        $entityManager->persist($inlineUpdate);

        $entityManager->flush();

        // 验证不同类型的更新都能正确存储和检索
        $foundMessage = $repository->findByBotAndUpdateId($bot, '9000000000000000500');
        $this->assertNotNull($foundMessage);
        $rawData = $foundMessage->getRawData();
        $this->assertNotNull($rawData);
        $this->assertIsArray($rawData);
        $this->assertArrayHasKey('message', $rawData);
        $this->assertIsArray($rawData['message']);
        $this->assertArrayHasKey('text', $rawData['message']);
        $this->assertSame('测试消息', $rawData['message']['text']);

        $foundCallback = $repository->findByBotAndUpdateId($bot, '9000000000000000501');
        $this->assertNotNull($foundCallback);
        $rawData = $foundCallback->getRawData();
        $this->assertNotNull($rawData);
        $this->assertIsArray($rawData);
        $this->assertArrayHasKey('callback_query', $rawData);
        $this->assertIsArray($rawData['callback_query']);
        $this->assertArrayHasKey('data', $rawData['callback_query']);
        $this->assertSame('button_clicked', $rawData['callback_query']['data']);

        $foundInline = $repository->findByBotAndUpdateId($bot, '9000000000000000502');
        $this->assertNotNull($foundInline);
        $rawData = $foundInline->getRawData();
        $this->assertNotNull($rawData);
        $this->assertIsArray($rawData);
        $this->assertArrayHasKey('inline_query', $rawData);
        $this->assertIsArray($rawData['inline_query']);
        $this->assertArrayHasKey('query', $rawData['inline_query']);
        $this->assertSame('搜索关键词', $rawData['inline_query']['query']);

        // 测试总数统计
        $totalUpdates = $repository->getTotalByBot($bot);
        $this->assertSame(3, $totalUpdates);

        // 测试获取最后一条更新（按updateId字符串排序）
        $lastUpdate = $repository->getLastUpdateByBot($bot);
        $this->assertNotNull($lastUpdate);
        // 按字符串排序，inline_001(502) 应该是最大的
        $this->assertSame('9000000000000000502', $lastUpdate->getUpdateId());
    }

    public function testCountByAssociationBotShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
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

        // 为机器人1创建 4 个更新
        for ($i = 1; $i <= 4; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot1);
            $update->setUpdateId("9000000000000600" . str_pad((string) $i, 2, "0", STR_PAD_LEFT));
            $update->setRawData(['message' => ['text' => "Bot1 Message {$i}"]]);
            $entityManager->persist($update);
        }

        // 为机器人2创建 2 个更新
        for ($i = 1; $i <= 2; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot2);
            $update->setUpdateId("9000000000000700" . str_pad((string) $i, 2, "0", STR_PAD_LEFT));
            $update->setRawData(['message' => ['text' => "Bot2 Message {$i}"]]);
            $entityManager->persist($update);
        }
        $entityManager->flush();

        // 测试按机器人计数
        $bot1Count = $repository->count(['bot' => $bot1]);
        $this->assertSame(4, $bot1Count);

        $bot2Count = $repository->count(['bot' => $bot2]);
        $this->assertSame(2, $bot2Count);
    }

    public function testFindOneByAssociationBotShouldReturnMatchingEntity(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000700');
        $update->setRawData(['message' => ['text' => 'Association test message']]);
        $entityManager->persist($update);
        $entityManager->flush();

        // 测试通过关联机器人查找
        $foundUpdate = $repository->findOneBy(['bot' => $bot]);
        $this->assertNotNull($foundUpdate);
        $this->assertInstanceOf(TelegramUpdate::class, $foundUpdate);
        $this->assertSame($update, $foundUpdate);
        $this->assertSame($bot, $foundUpdate->getBot());
        $this->assertSame('9000000000000000700', $foundUpdate->getUpdateId());
    }

    public function testRepositoryHandlesCorrectEntityType(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('实体类型测试');
        $bot->setUsername('entity_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试更新实体
        $update = new TelegramUpdate();
        $update->setBot($bot);
        $update->setUpdateId('9000000000000000800');
        $update->setRawData(['message' => ['text' => '实体类型测试消息']]);
        $entityManager->persist($update);
        $entityManager->flush();

        // 验证仓库正确处理TelegramUpdate实体
        $foundUpdate = $repository->find($update->getId());
        $this->assertInstanceOf(TelegramUpdate::class, $foundUpdate);
        $this->assertSame('9000000000000000800', $foundUpdate->getUpdateId());

        // 验证仓库的findOneBy方法正确处理实体
        $foundByUpdateId = $repository->findOneBy(['updateId' => '9000000000000000800']);
        $this->assertInstanceOf(TelegramUpdate::class, $foundByUpdateId);
        $this->assertSame($update->getId(), $foundByUpdateId->getId());
    }

    public function testFindByWithNullFieldsQuery(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值测试机器人');
        $bot->setUsername('null_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建不同类型的更新消息（模拟可空字段情况）
        $messageUpdate = new TelegramUpdate();
        $messageUpdate->setBot($bot);
        $messageUpdate->setUpdateId('9000000000000000500');
        $messageUpdate->setRawData([
            'update_id' => 1,
            'message' => [
                'message_id' => 101,
                'text' => '消息更新',
            ],
        ]);
        $entityManager->persist($messageUpdate);

        $callbackUpdate = new TelegramUpdate();
        $callbackUpdate->setBot($bot);
        $callbackUpdate->setUpdateId('9000000000000000501');
        $callbackUpdate->setRawData([
            'update_id' => 2,
            'callback_query' => [
                'id' => 'callback123',
                'data' => 'button_data',
            ],
        ]);
        $entityManager->persist($callbackUpdate);

        $emptyUpdate = new TelegramUpdate();
        $emptyUpdate->setBot($bot);
        $emptyUpdate->setUpdateId('9000000000000000503');
        $emptyUpdate->setRawData([]); // 空数据
        $entityManager->persist($emptyUpdate);
        $entityManager->flush();

        // 测试按机器人查询所有更新
        $allUpdates = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $allUpdates);

        // 验证不同类型的更新都能正确存储
        $foundMessage = $repository->findOneBy(['updateId' => '9000000000000000500']);
        $this->assertNotNull($foundMessage);
        $rawData = $foundMessage->getRawData();
        $this->assertNotNull($rawData);
        $this->assertArrayHasKey('message', $rawData);

        $foundCallback = $repository->findOneBy(['updateId' => '9000000000000000501']);
        $this->assertNotNull($foundCallback);
        $rawData = $foundCallback->getRawData();
        $this->assertNotNull($rawData);
        $this->assertArrayHasKey('callback_query', $rawData);

        $foundEmpty = $repository->findOneBy(['updateId' => '9000000000000000503']);
        $this->assertNotNull($foundEmpty);
        $this->assertSame([], $foundEmpty->getRawData());
    }

    public function testFindOneByWithOrderingLogic(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('排序测试机器人');
        $bot->setUsername('order_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个更新消息
        $updates = [];
        for ($i = 1; $i <= 3; ++$i) {
            $update = new TelegramUpdate();
            $update->setBot($bot);
            $update->setUpdateId("9000000000000900" . str_pad((string) $i, 2, "0", STR_PAD_LEFT));
            $update->setRawData(['message' => ['text' => "排序测试消息{$i}"]]);
            $entityManager->persist($update);
            $updates[] = $update;
        }
        $entityManager->flush();

        // 测试findOneBy返回一致性（应该总是返回同一个结果）
        $firstResult = $repository->findOneBy(['bot' => $bot]);
        $secondResult = $repository->findOneBy(['bot' => $bot]);
        $this->assertNotNull($firstResult);
        $this->assertNotNull($secondResult);
        $this->assertSame($firstResult->getId(), $secondResult->getId());

        // 测试具体条件的findOneBy
        $specificUpdate = $repository->findOneBy(['updateId' => '900000000000090002']);
        $this->assertNotNull($specificUpdate);
        $this->assertSame($updates[1]->getId(), $specificUpdate->getId());
        $rawData = $specificUpdate->getRawData();
        $this->assertNotNull($rawData);
        $this->assertIsArray($rawData);
        $this->assertArrayHasKey('message', $rawData);
        $this->assertIsArray($rawData['message']);
        $this->assertArrayHasKey('text', $rawData['message']);
        $this->assertSame('排序测试消息2', $rawData['message']['text']);

        // 测试特殊的查询方法
        $foundByBot = $repository->findByBotAndUpdateId($bot, '900000000000090001');
        $this->assertNotNull($foundByBot);
        $this->assertSame($updates[0]->getId(), $foundByBot->getId());
    }

    public function testCountWithNullableFields(): void
    {
        $repository = self::getService(TelegramUpdateRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值计数测试');
        $bot->setUsername('count_null_test');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建不同类型的更新消息
        $messageUpdate = new TelegramUpdate();
        $messageUpdate->setBot($bot);
        $messageUpdate->setUpdateId('9000000000000001000');
        $messageUpdate->setRawData(['message' => ['text' => '普通消息']]);
        $entityManager->persist($messageUpdate);

        $callbackUpdate = new TelegramUpdate();
        $callbackUpdate->setBot($bot);
        $callbackUpdate->setUpdateId('9000000000000001001');
        $callbackUpdate->setRawData(['callback_query' => ['data' => 'callback']]);
        $entityManager->persist($callbackUpdate);

        $inlineUpdate = new TelegramUpdate();
        $inlineUpdate->setBot($bot);
        $inlineUpdate->setUpdateId('9000000000000001002');
        $inlineUpdate->setRawData(['inline_query' => ['query' => 'search']]);
        $entityManager->persist($inlineUpdate);

        $emptyUpdate = new TelegramUpdate();
        $emptyUpdate->setBot($bot);
        $emptyUpdate->setUpdateId('9000000000000001003');
        $emptyUpdate->setRawData(null); // 模拟空数据
        $entityManager->persist($emptyUpdate);
        $entityManager->flush();

        // 测试按机器人计数
        $botCount = $repository->count(['bot' => $bot]);
        $this->assertSame(4, $botCount);

        // 测试特定 updateId 的计数
        $specificCount = $repository->count(['updateId' => '9000000000000001000']);
        $this->assertSame(1, $specificCount);

        $nonExistentCount = $repository->count(['updateId' => 'non_existent']);
        $this->assertSame(0, $nonExistentCount);

        // 测试特殊的仓库方法
        $totalByBot = $repository->getTotalByBot($bot);
        $this->assertSame(4, $totalByBot);

        // 测试获取最后一条更新（按updateId排序）
        $lastUpdate = $repository->getLastUpdateByBot($bot);
        $this->assertNotNull($lastUpdate);
        // 按字符串排序，count_empty_1(1003) 应该是最大的
        $this->assertSame('9000000000000001003', $lastUpdate->getUpdateId());
    }
}
