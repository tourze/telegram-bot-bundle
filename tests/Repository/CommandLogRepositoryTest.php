<?php

namespace TelegramBotBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\CommandLog;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\CommandLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CommandLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class CommandLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): CommandLogRepository
    {
        return self::getService(CommandLogRepository::class);
    }

    protected function createNewEntity(): CommandLog
    {
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);

        $log = new CommandLog();
        $log->setBot($bot);
        $log->setUserId(123456);
        $log->setUsername('testuser');
        $log->setChatId(789012);
        $log->setChatType('private');
        $log->setCommand('test');
        $log->setArgs(['arg1', 'arg2']);

        return $log;
    }

    protected function onSetUp(): void
    {
    }

    public function testConstruction(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $this->assertInstanceOf(CommandLogRepository::class, $repository);
    }

    public function testRepositoryHandlesCorrectEntityType(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('实体类型测试');
        $bot->setUsername('entity_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令日志实体
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('entity_test');
        $log->setUserId(123456);
        $log->setUsername('EntityTestUser');
        $log->setArgs(['/entity_test']);
        $log->setChatId(789012);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        // 验证仓库正确处理CommandLog实体
        $foundLog = $repository->find($log->getId());
        $this->assertInstanceOf(CommandLog::class, $foundLog);
        $this->assertSame('entity_test', $foundLog->getCommand());

        // 验证仓库的findOneBy方法正确处理实体
        $foundByCommand = $repository->findOneBy(['command' => 'entity_test']);
        $this->assertInstanceOf(CommandLog::class, $foundByCommand);
        $this->assertSame($log->getId(), $foundByCommand->getId());
    }

    public function testFind(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('test');
        $log->setUserId(123456);
        $log->setUsername('TestUser');
        $log->setArgs(['/test', 'command']);
        $log->setChatId(789012);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        $logId = $log->getId();
        $this->assertNotNull($logId);

        // 测试查找存在的实体
        $foundLog = $repository->find($logId);
        $this->assertNotNull($foundLog);
        $this->assertSame($log, $foundLog);
        $this->assertSame('test', $foundLog->getCommand());

        // 测试查找不存在的实体
        $notFoundLog = $repository->find('nonexistent-id');
        $this->assertNull($notFoundLog);

        // 测试查找null ID - 跳过该测试以避免异常
        // $nullLog = $repository->find(null);
        // $this->assertNull($nullLog);
    }

    public function testFindBy(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试日志
        $log1 = new CommandLog();
        $log1->setBot($bot);
        $log1->setCommand('start');
        $log1->setUserId(123456);
        $log1->setUsername('User1');
        $log1->setArgs(['/start']);
        $log1->setChatId(111111);
        $log1->setChatType('private');
        $log1->setIsSystem(false);
        $entityManager->persist($log1);

        $log2 = new CommandLog();
        $log2->setBot($bot);
        $log2->setCommand('help');
        $log2->setUserId(789012);
        $log2->setUsername('User2');
        $log2->setArgs(['/help']);
        $log2->setChatId(222222);
        $log2->setChatType('group');
        $log2->setIsSystem(false);
        $entityManager->persist($log2);

        $log3 = new CommandLog();
        $log3->setBot($bot);
        $log3->setCommand('start');
        $log3->setUserId(345678);
        $log3->setUsername('User3');
        $log3->setArgs(['/start']);
        $log3->setChatId(333333);
        $log3->setChatType('private');
        $log3->setIsSystem(false);
        $entityManager->persist($log3);

        $entityManager->flush();

        // 测试按机器人查找
        $logsByBot = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $logsByBot);

        // 测试按命令查找（限制在当前机器人）
        $startLogs = $repository->findBy(['bot' => $bot, 'command' => 'start']);
        $this->assertCount(2, $startLogs);

        // 测试按用户ID查找
        $user1Logs = $repository->findBy(['userId' => 123456]);
        $this->assertCount(1, $user1Logs);
        $this->assertSame($log1, $user1Logs[0]);

        // 测试组合条件查找
        $startBotLogs = $repository->findBy(['bot' => $bot, 'command' => 'start']);
        $this->assertCount(2, $startBotLogs);

        // 测试排序
        $sortedLogs = $repository->findBy(['bot' => $bot], ['id' => 'ASC']);
        $this->assertCount(3, $sortedLogs);
        $this->assertSame($log1, $sortedLogs[0]);
        $this->assertSame($log2, $sortedLogs[1]);
        $this->assertSame($log3, $sortedLogs[2]);

        // 测试限制数量
        $limitedLogs = $repository->findBy(['bot' => $bot], null, 2);
        $this->assertCount(2, $limitedLogs);

        // 测试偏移量
        $offsetLogs = $repository->findBy(['bot' => $bot], ['id' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetLogs);
        $this->assertSame($log2, $offsetLogs[0]);
        $this->assertSame($log3, $offsetLogs[1]);

        // 测试空条件
        $emptyResults = $repository->findBy(['command' => 'nonexistent']);
        $this->assertCount(0, $emptyResults);
    }

    public function testFindOneBy(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('unique');
        $log->setUserId(123456);
        $log->setUsername('UniqueUser');
        $log->setArgs(['/unique', 'command']);
        $log->setChatId(444444);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        // 测试查找唯一实体
        $foundLog = $repository->findOneBy(['command' => 'unique']);
        $this->assertNotNull($foundLog);
        $this->assertSame($log, $foundLog);
        $this->assertSame('unique', $foundLog->getCommand());

        // 测试查找不存在的实体
        $notFoundLog = $repository->findOneBy(['command' => 'nonexistent']);
        $this->assertNull($notFoundLog);

        // 测试组合条件查找
        $complexFoundLog = $repository->findOneBy(['bot' => $bot, 'userId' => '123456']);
        $this->assertNotNull($complexFoundLog);
        $this->assertSame($log, $complexFoundLog);
    }

    public function testFindAll(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 初始状态应该为空
        $initialLogs = $repository->findAll();
        $this->assertIsArray($initialLogs);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试日志
        for ($i = 1; $i <= 3; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot);
            $log->setCommand("command{$i}");
            $log->setUserId(1000 + $i);
            $log->setUsername("User{$i}");
            $log->setArgs(["/command{$i}"]);
            $log->setChatId(5000 + $i);
            $log->setChatType('private');
            $log->setIsSystem(false);
            $entityManager->persist($log);
        }
        $entityManager->flush();

        // 测试获取所有实体
        $allLogs = $repository->findAll();
        $this->assertGreaterThanOrEqual(3, count($allLogs)); // 允许之前测试留下的数据
        $this->assertContainsOnlyInstancesOf(CommandLog::class, $allLogs);

        // 验证我们创建的日志确实存在
        $ourLogs = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $ourLogs);
    }

    public function testCount(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 初始计数
        $initialCount = $repository->count([]);
        $this->assertGreaterThanOrEqual(0, $initialCount);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试日志
        for ($i = 1; $i <= 5; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot);
            $log->setCommand($i <= 3 ? 'start' : 'help'); // 前3个start，后2个help
            $log->setUserId(2000 + $i);
            $log->setUsername("User{$i}");
            $log->setArgs(["/command{$i}"]);
            $log->setChatId(6000 + $i);
            $log->setChatType(0 === $i % 2 ? 'group' : 'private');
            $log->setIsSystem(false);
            $entityManager->persist($log);
        }
        $entityManager->flush();

        // 测试总计数
        $totalCount = $repository->count([]);
        $this->assertSame($initialCount + 5, $totalCount);

        // 测试条件计数（限制在当前机器人）
        $startCount = $repository->count(['bot' => $bot, 'command' => 'start']);
        $this->assertSame(3, $startCount);

        $helpCount = $repository->count(['bot' => $bot, 'command' => 'help']);
        $this->assertSame(2, $helpCount);

        // 测试组合条件计数
        $botStartCount = $repository->count(['bot' => $bot, 'command' => 'start']);
        $this->assertSame(3, $botStartCount);
    }

    public function testSave(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 创建新日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('new');
        $log->setUserId(123456);
        $log->setUsername('NewUser');
        $log->setArgs(['/new', 'command']);
        $log->setChatId(777777);
        $log->setChatType('private');
        $log->setIsSystem(false);

        // 测试保存但不刷新
        $repository->save($log, false);
        $this->assertSame(0, $log->getId()); // 没有刷新，ID应该仍为0

        // 手动刷新
        $entityManager->flush();
        $this->assertGreaterThan(0, $log->getId());

        // 测试默认保存（自动刷新）
        $log2 = new CommandLog();
        $log2->setBot($bot);
        $log2->setCommand('another');
        $log2->setUserId(789012);
        $log2->setUsername('AnotherUser');
        $log2->setArgs(['/another', 'command']);
        $log2->setChatId(888888);
        $log2->setChatType('group');
        $log2->setIsSystem(false);

        $repository->save($log2);
        $this->assertGreaterThan(0, $log2->getId()); // 应该有ID

        // 验证保存的数据
        $savedLog = $repository->find($log2->getId());
        $this->assertNotNull($savedLog);
        $this->assertSame('another', $savedLog->getCommand());
    }

    public function testRemove(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('delete');
        $log->setUserId(123456);
        $log->setUsername('DeleteUser');
        $log->setArgs(['/delete', 'command']);
        $log->setChatId(999999);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        $logId = $log->getId();
        $this->assertNotNull($logId);

        // 验证日志存在
        $existingLog = $repository->find($logId);
        $this->assertNotNull($existingLog);

        // 测试删除但不刷新
        $repository->remove($log, false);
        $stillExists = $repository->find($logId);
        $this->assertNotNull($stillExists); // 没有刷新，应该还存在

        // 手动刷新
        $entityManager->flush();
        $deletedLog = $repository->find($logId);
        $this->assertNull($deletedLog); // 应该被删除

        // 测试默认删除（自动刷新）
        $log2 = new CommandLog();
        $log2->setBot($bot);
        $log2->setCommand('delete2');
        $log2->setUserId(789012);
        $log2->setUsername('Delete2User');
        $log2->setArgs(['/delete2', 'command']);
        $log2->setChatId(1010101);
        $log2->setChatType('group');
        $log2->setIsSystem(false);
        $entityManager->persist($log2);
        $entityManager->flush();

        $log2Id = $log2->getId();
        $this->assertNotNull($log2Id);

        $repository->remove($log2);
        $deletedLog2 = $repository->find($log2Id);
        $this->assertNull($deletedLog2); // 应该立即被删除
    }

    public function testEntityRelationships(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('relation');
        $log->setUserId(123456);
        $log->setUsername('RelationUser');
        $log->setArgs(['/relation', 'command']);
        $log->setChatId(1111111);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        // 测试机器人关联
        $foundLog = $repository->find($log->getId());
        $this->assertNotNull($foundLog);
        $this->assertSame($bot, $foundLog->getBot());
        $this->assertSame('test_bot', $foundLog->getBot()->getUsername());

        // 测试级联查询
        $logsWithBot = $repository->createQueryBuilder('l')
            ->join('l.bot', 'b')
            ->where('b.username = :username')
            ->setParameter('username', 'test_bot')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($logsWithBot);
        $this->assertCount(1, $logsWithBot);
        $this->assertArrayHasKey(0, $logsWithBot);
        $this->assertSame($log, $logsWithBot[0]);
    }

    public function testBoundaryConditions(): void
    {
        $repository = self::getService(CommandLogRepository::class);
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
        $emptyResults = $repository->findBy(['command' => 'nonexistent']);
        $this->assertIsArray($emptyResults);
        $this->assertCount(0, $emptyResults);

        // 测试大数据量查询
        for ($i = 1; $i <= 100; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot);
            $log->setCommand('bulk');
            $log->setUserId(3000 + $i);
            $log->setUsername("User{$i}");
            $log->setArgs(['/bulk', 'command']);
            $log->setChatId(7000 + $i);
            $log->setChatType(0 === $i % 3 ? 'supergroup' : (0 === $i % 2 ? 'group' : 'private'));
            $log->setIsSystem(false);
            $entityManager->persist($log);
        }
        $entityManager->flush();

        $bulkCount = $repository->count(['command' => 'bulk']);
        $this->assertSame(100, $bulkCount);

        // 测试分页查询
        $pagedResults = $repository->findBy(['command' => 'bulk'], null, 10, 0);
        $this->assertCount(10, $pagedResults);

        // 测试时间记录（CreateTimeAware trait）
        $futureLog = new CommandLog();
        $futureLog->setBot($bot);
        $futureLog->setCommand('future');
        $futureLog->setUserId(999999);
        $futureLog->setUsername('FutureUser');
        $futureLog->setArgs(['/future', 'command']);
        $futureLog->setChatId(8888888);
        $futureLog->setChatType('private');
        $futureLog->setIsSystem(false);
        $entityManager->persist($futureLog);
        $entityManager->flush();

        $futureResult = $repository->findOneBy(['command' => 'future']);
        $this->assertNotNull($futureResult);
        $this->assertNotNull($futureResult->getCreateTime()); // 验证CreateTime被自动设置
    }

    public function testSpecialCharactersAndUnicode(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 测试特殊字符和Unicode
        $specialLog = new CommandLog();
        $specialLog->setBot($bot);
        $specialLog->setCommand('特殊命令🚀');
        $specialLog->setUserId(123123);
        $specialLog->setUsername('特殊用户😀');
        $specialLog->setArgs(['/特殊命令', 'with', 'emojis', '🚀💫']);
        $specialLog->setChatId(9999999);
        $specialLog->setChatType('private');
        $specialLog->setIsSystem(false);
        $entityManager->persist($specialLog);
        $entityManager->flush();

        // 验证特殊字符和Unicode能正确存储和检索
        $foundSpecialLog = $repository->findOneBy(['command' => '特殊命令🚀']);
        $this->assertNotNull($foundSpecialLog);
        $this->assertSame('特殊用户😀', $foundSpecialLog->getUsername());
        $args = $foundSpecialLog->getArgs();
        $this->assertNotNull($args);
        $this->assertContains('🚀💫', $args);
        $this->assertSame(123123, $foundSpecialLog->getUserId());
    }

    public function testFindByWithNullFieldsQuery(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值测试机器人');
        $bot->setUsername('null_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建系统命令日志（isSystem = true）
        $systemLog = new CommandLog();
        $systemLog->setBot($bot);
        $systemLog->setCommand('system_cmd');
        $systemLog->setUserId(0); // 系统命令可能使用0作为用户ID
        $systemLog->setUsername(null); // 系统命令可能没有用户名
        $systemLog->setArgs(['/system']);
        $systemLog->setChatId(0);
        $systemLog->setChatType('private');
        $systemLog->setIsSystem(true);
        $entityManager->persist($systemLog);

        // 创建普通用户命令日志
        $userLog = new CommandLog();
        $userLog->setBot($bot);
        $userLog->setCommand('user_cmd');
        $userLog->setUserId(12345);
        $userLog->setUsername('TestUser');
        $userLog->setArgs(['/user']);
        $userLog->setChatId(67890);
        $userLog->setChatType('private');
        $userLog->setIsSystem(false);
        $entityManager->persist($userLog);
        $entityManager->flush();

        // 测试查询系统命令
        $systemLogs = $repository->findBy(['isSystem' => true]);
        $this->assertGreaterThanOrEqual(1, count($systemLogs));

        // 测试查询非系统命令
        $userLogs = $repository->findBy(['isSystem' => false]);
        $this->assertGreaterThanOrEqual(1, count($userLogs));

        // 验证系统日志的特殊字段
        $foundSystemLog = $repository->findOneBy(['command' => 'system_cmd']);
        $this->assertNotNull($foundSystemLog);
        $this->assertTrue($foundSystemLog->isSystem());
        $this->assertSame(0, $foundSystemLog->getUserId());

        // 验证用户日志
        $foundUserLog = $repository->findOneBy(['command' => 'user_cmd']);
        $this->assertNotNull($foundUserLog);
        $this->assertFalse($foundUserLog->isSystem());
        $this->assertSame(12345, $foundUserLog->getUserId());
    }

    public function testFindOneByWithOrderingLogic(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('排序测试机器人');
        $bot->setUsername('order_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个相同命令的日志
        $logs = [];
        for ($i = 1; $i <= 3; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot);
            $log->setCommand('start');
            $log->setUserId(1000 + $i);
            $log->setUsername("User{$i}");
            $log->setArgs(['/start']);
            $log->setChatId(2000 + $i);
            $log->setChatType('private');
            $log->setIsSystem(false);
            $entityManager->persist($log);
            $logs[] = $log;
        }
        $entityManager->flush();

        // 测试findOneBy返回一致性（应该总是返回同一个结果）
        $firstResult = $repository->findOneBy(['command' => 'start']);
        $secondResult = $repository->findOneBy(['command' => 'start']);
        $this->assertNotNull($firstResult);
        $this->assertNotNull($secondResult);
        $this->assertSame($firstResult->getId(), $secondResult->getId());

        // 测试具体条件的findOneBy
        $specificLog = $repository->findOneBy(['userId' => 1002]);
        $this->assertNotNull($specificLog);
        $this->assertSame($logs[1]->getId(), $specificLog->getId());
        $this->assertSame('User2', $specificLog->getUsername());
    }

    public function testCountWithNullableFields(): void
    {
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值计数测试');
        $bot->setUsername('count_null_test');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建不同类型的日志
        $systemLog = new CommandLog();
        $systemLog->setBot($bot);
        $systemLog->setCommand('system');
        $systemLog->setUserId(0);
        $systemLog->setUsername(null); // 可能为null的字段
        $systemLog->setArgs(['/system']);
        $systemLog->setChatId(0);
        $systemLog->setChatType('private');
        $systemLog->setIsSystem(true);
        $entityManager->persist($systemLog);

        $userLog1 = new CommandLog();
        $userLog1->setBot($bot);
        $userLog1->setCommand('help');
        $userLog1->setUserId(123);
        $userLog1->setUsername('User1');
        $userLog1->setArgs(['/help']);
        $userLog1->setChatId(456);
        $userLog1->setChatType('private');
        $userLog1->setIsSystem(false);
        $entityManager->persist($userLog1);

        $userLog2 = new CommandLog();
        $userLog2->setBot($bot);
        $userLog2->setCommand('start');
        $userLog2->setUserId(789);
        $userLog2->setUsername('User2');
        $userLog2->setArgs(['/start']);
        $userLog2->setChatId(101112);
        $userLog2->setChatType('group');
        $userLog2->setIsSystem(false);
        $entityManager->persist($userLog2);
        $entityManager->flush();

        // 测试按系统标记计数
        $systemCount = $repository->count(['isSystem' => true]);
        $this->assertGreaterThanOrEqual(1, $systemCount);

        $userCount = $repository->count(['isSystem' => false]);
        $this->assertGreaterThanOrEqual(2, $userCount);

        // 测试按聊天类型计数
        $privateCount = $repository->count(['chatType' => 'private']);
        $this->assertGreaterThanOrEqual(2, $privateCount);

        $groupCount = $repository->count(['chatType' => 'group']);
        $this->assertGreaterThanOrEqual(1, $groupCount);

        // 测试特定机器人的计数
        $botLogCount = $repository->count(['bot' => $bot]);
        $this->assertSame(3, $botLogCount);
    }

    public function testCountByAssociationBotShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(CommandLogRepository::class);
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

        // 为机器人1创建 4 个日志
        for ($i = 1; $i <= 4; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot1);
            $log->setCommand("bot1_cmd{$i}");
            $log->setUserId(9000 + $i);
            $log->setUsername("Bot1User{$i}");
            $log->setArgs(["/bot1_cmd{$i}"]);
            $log->setChatId(13000 + $i);
            $log->setChatType('private');
            $log->setIsSystem(false);
            $entityManager->persist($log);
        }

        // 为机器人2创建 2 个日志
        for ($i = 1; $i <= 2; ++$i) {
            $log = new CommandLog();
            $log->setBot($bot2);
            $log->setCommand("bot2_cmd{$i}");
            $log->setUserId(10000 + $i);
            $log->setUsername("Bot2User{$i}");
            $log->setArgs(["/bot2_cmd{$i}"]);
            $log->setChatId(14000 + $i);
            $log->setChatType('group');
            $log->setIsSystem(false);
            $entityManager->persist($log);
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
        $repository = self::getService(CommandLogRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试日志
        $log = new CommandLog();
        $log->setBot($bot);
        $log->setCommand('association_test');
        $log->setUserId(123456);
        $log->setUsername('AssociationUser');
        $log->setArgs(['/association_test']);
        $log->setChatId(888888);
        $log->setChatType('private');
        $log->setIsSystem(false);
        $entityManager->persist($log);
        $entityManager->flush();

        // 测试通过关联机器人查找
        $foundLog = $repository->findOneBy(['bot' => $bot]);
        $this->assertNotNull($foundLog);
        $this->assertInstanceOf(CommandLog::class, $foundLog);
        $this->assertSame($log, $foundLog);
        $this->assertSame($bot, $foundLog->getBot());
        $this->assertSame('association_test', $foundLog->getCommand());
    }
}
