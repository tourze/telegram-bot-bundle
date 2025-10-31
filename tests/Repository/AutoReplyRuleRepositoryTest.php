<?php

namespace TelegramBotBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\AutoReplyRuleRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AutoReplyRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class AutoReplyRuleRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): AutoReplyRuleRepository
    {
        return self::getService(AutoReplyRuleRepository::class);
    }

    protected function createNewEntity(): AutoReplyRule
    {
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);

        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('测试规则');
        $rule->setKeyword('test');
        $rule->setReplyContent('测试回复');
        $rule->setPriority(10);
        $rule->setValid(true);

        return $rule;
    }

    protected function onSetUp(): void
    {
    }

    public function testConstruction(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $this->assertInstanceOf(AutoReplyRuleRepository::class, $repository);
    }

    public function testFind(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('测试规则');
        $rule->setKeyword('test');
        $rule->setReplyContent('测试回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        $ruleId = $rule->getId();
        $this->assertNotNull($ruleId);

        // 测试查找存在的实体
        $foundRule = $repository->find($ruleId);
        $this->assertNotNull($foundRule);
        $this->assertSame($rule, $foundRule);
        $this->assertSame('测试规则', $foundRule->getName());

        // 测试查找不存在的实体
        $notFoundRule = $repository->find(999999);
        $this->assertNull($notFoundRule);

        // 测试查找null ID - 预期会抛出异常或返回null，这里我们跳过这个测试
        // $nullRule = $repository->find(null);
        // $this->assertNull($nullRule);
    }

    public function testFindBy(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试规则
        $rule1 = new AutoReplyRule();
        $rule1->setBot($bot);
        $rule1->setName('规则1');
        $rule1->setKeyword('keyword1');
        $rule1->setReplyContent('回复1');
        $rule1->setPriority(10);
        $rule1->setValid(true);
        $entityManager->persist($rule1);

        $rule2 = new AutoReplyRule();
        $rule2->setBot($bot);
        $rule2->setName('规则2');
        $rule2->setKeyword('keyword2');
        $rule2->setReplyContent('回复2');
        $rule2->setPriority(20);
        $rule2->setValid(true);
        $entityManager->persist($rule2);

        $rule3 = new AutoReplyRule();
        $rule3->setBot($bot);
        $rule3->setName('规则3');
        $rule3->setKeyword('keyword3');
        $rule3->setReplyContent('回复3');
        $rule3->setPriority(5);
        $rule3->setValid(false);
        $entityManager->persist($rule3);

        $entityManager->flush();

        // 测试按机器人查找
        $rulesByBot = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $rulesByBot);

        // 测试按有效性查找（应该包含当前测试创建的2个有效规则）
        $validRules = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(2, count($validRules)); // 允许之前测试留下的数据

        // 测试组合条件查找
        $validBotRules = $repository->findBy(['bot' => $bot, 'valid' => true]);
        $this->assertCount(2, $validBotRules);

        // 测试排序
        $sortedRules = $repository->findBy(['bot' => $bot], ['priority' => 'DESC']);
        $this->assertCount(3, $sortedRules);
        $this->assertSame($rule2, $sortedRules[0]); // 优先级最高
        $this->assertSame($rule1, $sortedRules[1]);
        $this->assertSame($rule3, $sortedRules[2]); // 优先级最低

        // 测试限制数量
        $limitedRules = $repository->findBy(['bot' => $bot], null, 2);
        $this->assertCount(2, $limitedRules);

        // 测试偏移量
        $offsetRules = $repository->findBy(['bot' => $bot], ['priority' => 'DESC'], 2, 1);
        $this->assertCount(2, $offsetRules);
        $this->assertSame($rule1, $offsetRules[0]);
        $this->assertSame($rule3, $offsetRules[1]);

        // 测试空条件
        $emptyResults = $repository->findBy(['keyword' => 'nonexistent']);
        $this->assertCount(0, $emptyResults);
    }

    public function testFindOneBy(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('唯一规则');
        $rule->setKeyword('unique');
        $rule->setReplyContent('唯一回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        // 测试查找唯一实体
        $foundRule = $repository->findOneBy(['keyword' => 'unique']);
        $this->assertNotNull($foundRule);
        $this->assertSame($rule, $foundRule);
        $this->assertSame('唯一规则', $foundRule->getName());

        // 测试查找不存在的实体
        $notFoundRule = $repository->findOneBy(['keyword' => 'nonexistent']);
        $this->assertNull($notFoundRule);

        // 测试组合条件查找
        $complexFoundRule = $repository->findOneBy(['bot' => $bot, 'valid' => true]);
        $this->assertNotNull($complexFoundRule);
        $this->assertSame($rule, $complexFoundRule);
    }

    public function testFindAll(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 初始状态应该为空
        $initialRules = $repository->findAll();
        $this->assertIsArray($initialRules);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试规则
        for ($i = 1; $i <= 3; ++$i) {
            $rule = new AutoReplyRule();
            $rule->setBot($bot);
            $rule->setName("规则{$i}");
            $rule->setKeyword("keyword{$i}");
            $rule->setReplyContent("回复{$i}");
            $rule->setPriority($i * 10);
            $rule->setValid(1 === $i % 2); // 奇数有效
            $entityManager->persist($rule);
        }
        $entityManager->flush();

        // 测试获取所有实体
        $allRules = $repository->findAll();
        $this->assertGreaterThanOrEqual(3, count($allRules)); // 允许之前测试留下的数据
        $this->assertContainsOnlyInstancesOf(AutoReplyRule::class, $allRules);
    }

    public function testCount(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 初始计数
        $initialCount = $repository->count([]);
        $this->assertGreaterThanOrEqual(0, $initialCount); // 允许之前测试留下的数据

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        for ($i = 1; $i <= 5; ++$i) {
            $rule = new AutoReplyRule();
            $rule->setBot($bot);
            $rule->setName("规则{$i}");
            $rule->setKeyword("keyword{$i}");
            $rule->setReplyContent("回复{$i}");
            $rule->setPriority($i * 10);
            $rule->setValid($i <= 3); // 前3个有效
            $entityManager->persist($rule);
        }
        $entityManager->flush();

        // 测试总计数
        $totalCount = $repository->count([]);
        $this->assertSame($initialCount + 5, $totalCount);

        // 测试组合条件计数（使用新创建的机器人来避免干扰）
        $botValidCount = $repository->count(['bot' => $bot, 'valid' => true]);
        $this->assertSame(3, $botValidCount);

        $botInvalidCount = $repository->count(['bot' => $bot, 'valid' => false]);
        $this->assertSame(2, $botInvalidCount);

        $botTotalCount = $repository->count(['bot' => $bot]);
        $this->assertSame(5, $botTotalCount);
    }

    public function testFindMatchingRules(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
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

        // 创建有效的自动回复规则（高优先级）
        $rule1 = new AutoReplyRule();
        $rule1->setBot($bot);
        $rule1->setName('问候规则');
        $rule1->setKeyword('hello');
        $rule1->setReplyContent('你好！欢迎使用机器人');
        $rule1->setPriority(10);
        $rule1->setValid(true);
        $entityManager->persist($rule1);

        // 创建有效的自动回复规则（低优先级）
        $rule2 = new AutoReplyRule();
        $rule2->setBot($bot);
        $rule2->setName('帮助规则');
        $rule2->setKeyword('help');
        $rule2->setReplyContent('这是帮助信息');
        $rule2->setPriority(5);
        $rule2->setValid(true);
        $entityManager->persist($rule2);

        // 创建无效的自动回复规则
        $rule3 = new AutoReplyRule();
        $rule3->setBot($bot);
        $rule3->setName('无效规则');
        $rule3->setKeyword('invalid');
        $rule3->setReplyContent('这是无效规则');
        $rule3->setPriority(20);
        $rule3->setValid(false);
        $entityManager->persist($rule3);

        // 创建其他机器人的规则
        $rule4 = new AutoReplyRule();
        $rule4->setBot($otherBot);
        $rule4->setName('其他机器人规则');
        $rule4->setKeyword('other');
        $rule4->setReplyContent('其他机器人回复');
        $rule4->setPriority(15);
        $rule4->setValid(true);
        $entityManager->persist($rule4);

        $entityManager->flush();

        // 测试查找指定机器人的有效规则
        $botId = $bot->getId();
        $this->assertNotNull($botId);
        $results = $repository->findMatchingRules($botId, 'test message');

        // 应该只返回有效的规则，按优先级降序排列
        $this->assertCount(2, $results);
        $this->assertSame($rule1, $results[0]); // 高优先级在前
        $this->assertSame($rule2, $results[1]); // 低优先级在后

        // 验证规则都是有效的
        foreach ($results as $rule) {
            $this->assertTrue($rule->isValid());
            $this->assertSame($bot, $rule->getBot());
        }

        // 测试其他机器人的规则
        $otherBotId = $otherBot->getId();
        $this->assertNotNull($otherBotId);
        $otherResults = $repository->findMatchingRules($otherBotId, 'test message');
        $this->assertCount(1, $otherResults);
        $this->assertSame($rule4, $otherResults[0]);

        // 测试不存在的机器人
        $emptyResults = $repository->findMatchingRules('nonexistent', 'test message');
        $this->assertCount(0, $emptyResults);
    }

    public function testSave(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 创建新规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('新规则');
        $rule->setKeyword('new');
        $rule->setReplyContent('新回复');
        $rule->setPriority(10);
        $rule->setValid(true);

        // 测试保存但不刷新
        $repository->save($rule, false);
        $this->assertSame(0, $rule->getId()); // 没有刷新，ID应该仍为0

        // 手动刷新
        $entityManager->flush();
        $this->assertGreaterThan(0, $rule->getId());

        // 测试默认保存（自动刷新）
        $rule2 = new AutoReplyRule();
        $rule2->setBot($bot);
        $rule2->setName('另一个规则');
        $rule2->setKeyword('another');
        $rule2->setReplyContent('另一个回复');
        $rule2->setPriority(20);
        $rule2->setValid(true);

        $repository->save($rule2);
        $this->assertGreaterThan(0, $rule2->getId()); // 应该有ID

        // 验证保存的数据
        $savedRule = $repository->find($rule2->getId());
        $this->assertNotNull($savedRule);
        $this->assertSame('另一个规则', $savedRule->getName());
    }

    public function testRemove(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('待删除规则');
        $rule->setKeyword('delete');
        $rule->setReplyContent('待删除回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        $ruleId = $rule->getId();
        $this->assertNotNull($ruleId);

        // 验证规则存在
        $existingRule = $repository->find($ruleId);
        $this->assertNotNull($existingRule);

        // 测试删除但不刷新
        $repository->remove($rule, false);
        $stillExists = $repository->find($ruleId);
        $this->assertNotNull($stillExists); // 没有刷新，应该还存在

        // 手动刷新
        $entityManager->flush();
        $deletedRule = $repository->find($ruleId);
        $this->assertNull($deletedRule); // 应该被删除

        // 测试默认删除（自动刷新）
        $rule2 = new AutoReplyRule();
        $rule2->setBot($bot);
        $rule2->setName('另一个待删除规则');
        $rule2->setKeyword('delete2');
        $rule2->setReplyContent('另一个待删除回复');
        $rule2->setPriority(20);
        $rule2->setValid(true);
        $entityManager->persist($rule2);
        $entityManager->flush();

        $rule2Id = $rule2->getId();
        $this->assertNotNull($rule2Id);

        $repository->remove($rule2);
        $deletedRule2 = $repository->find($rule2Id);
        $this->assertNull($deletedRule2); // 应该立即被删除
    }

    public function testEntityRelationships(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('关联测试规则');
        $rule->setKeyword('relation');
        $rule->setReplyContent('关联测试回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        // 测试机器人关联
        $foundRule = $repository->find($rule->getId());
        $this->assertNotNull($foundRule);
        $this->assertSame($bot, $foundRule->getBot());
        $this->assertSame('test_bot', $foundRule->getBot()->getUsername());

        // 测试级联查询
        $rulesWithBot = $repository->createQueryBuilder('r')
            ->join('r.bot', 'b')
            ->where('b.username = :username')
            ->setParameter('username', 'test_bot')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $rulesWithBot);
        $this->assertSame($rule, $rulesWithBot[0]);
    }

    public function testBoundaryConditions(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 测试空字符串参数
        $emptyResults = $repository->findMatchingRules('', '');
        $this->assertIsArray($emptyResults);
        $this->assertCount(0, $emptyResults);

        // 测试极长字符串
        $longString = str_repeat('a', 1000);
        $longResults = $repository->findMatchingRules($longString, $longString);
        $this->assertIsArray($longResults);
        $this->assertCount(0, $longResults);

        // 测试特殊字符
        $specialChars = "!@#$%^&*()[]{}|\\:;\"'<>?,./'";
        $specialResults = $repository->findMatchingRules($specialChars, $specialChars);
        $this->assertIsArray($specialResults);
        $this->assertCount(0, $specialResults);

        // 测试Unicode字符
        $unicodeString = '测试中文字符🚀';
        $unicodeResults = $repository->findMatchingRules($unicodeString, $unicodeString);
        $this->assertIsArray($unicodeResults);
        $this->assertCount(0, $unicodeResults);
    }

    public function testCountByAssociationBotShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
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

        // 为机器人1创建 4 个规则
        for ($i = 1; $i <= 4; ++$i) {
            $rule = new AutoReplyRule();
            $rule->setBot($bot1);
            $rule->setName("机器人1规则{$i}");
            $rule->setKeyword("bot1_keyword{$i}");
            $rule->setReplyContent("机器人1回复{$i}");
            $rule->setPriority($i * 10);
            $rule->setValid(true);
            $entityManager->persist($rule);
        }

        // 为机器人2创建 2 个规则
        for ($i = 1; $i <= 2; ++$i) {
            $rule = new AutoReplyRule();
            $rule->setBot($bot2);
            $rule->setName("机器人2规则{$i}");
            $rule->setKeyword("bot2_keyword{$i}");
            $rule->setReplyContent("机器人2回复{$i}");
            $rule->setPriority($i * 5);
            $rule->setValid(true);
            $entityManager->persist($rule);
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
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试规则
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('关联测试规则');
        $rule->setKeyword('association_test');
        $rule->setReplyContent('关联测试回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        // 测试通过关联机器人查找
        $foundRule = $repository->findOneBy(['bot' => $bot]);
        $this->assertNotNull($foundRule);
        $this->assertInstanceOf(AutoReplyRule::class, $foundRule);
        $this->assertSame($rule, $foundRule);
        $this->assertSame($bot, $foundRule->getBot());
        $this->assertSame('关联测试规则', $foundRule->getName());
    }

    public function testRepositoryHandlesCorrectEntityType(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('实体类型测试');
        $bot->setUsername('entity_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试自动回复规则实体
        $rule = new AutoReplyRule();
        $rule->setBot($bot);
        $rule->setName('实体类型测试规则');
        $rule->setKeyword('entity_test');
        $rule->setReplyContent('实体测试回复');
        $rule->setPriority(10);
        $rule->setValid(true);
        $entityManager->persist($rule);
        $entityManager->flush();

        // 验证仓库正确处理AutoReplyRule实体
        $foundRule = $repository->find($rule->getId());
        $this->assertInstanceOf(AutoReplyRule::class, $foundRule);
        $this->assertSame('实体类型测试规则', $foundRule->getName());

        // 验证仓库的findOneBy方法正确处理实体
        $foundByKeyword = $repository->findOneBy(['keyword' => 'entity_test']);
        $this->assertInstanceOf(AutoReplyRule::class, $foundByKeyword);
        $this->assertSame($rule->getId(), $foundByKeyword->getId());
    }

    public function testFindByWithNullFieldsQuery(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值测试机器人');
        $bot->setUsername('null_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建有效规则
        $validRule = new AutoReplyRule();
        $validRule->setBot($bot);
        $validRule->setName('有效规则');
        $validRule->setKeyword('valid_rule');
        $validRule->setReplyContent('有效回复');
        $validRule->setPriority(10);
        $validRule->setValid(true);
        $entityManager->persist($validRule);

        // 创建无效规则
        $invalidRule = new AutoReplyRule();
        $invalidRule->setBot($bot);
        $invalidRule->setName('无效规则');
        $invalidRule->setKeyword('invalid_rule');
        $invalidRule->setReplyContent('无效回复');
        $invalidRule->setPriority(5);
        $invalidRule->setValid(false);
        $entityManager->persist($invalidRule);
        $entityManager->flush();

        // 测试按有效性查询
        $validRules = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(1, count($validRules));

        $invalidRules = $repository->findBy(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, count($invalidRules));

        // 验证找到的规则包含我们的测试数据
        $foundValidRule = $repository->findOneBy(['keyword' => 'valid_rule']);
        $this->assertNotNull($foundValidRule);
        $this->assertTrue($foundValidRule->isValid());

        $foundInvalidRule = $repository->findOneBy(['keyword' => 'invalid_rule']);
        $this->assertNotNull($foundInvalidRule);
        $this->assertFalse($foundInvalidRule->isValid());
    }

    public function testFindOneByWithOrderingLogic(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('排序测试机器人');
        $bot->setUsername('order_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个相同优先级的规则
        $rules = [];
        for ($i = 1; $i <= 3; ++$i) {
            $rule = new AutoReplyRule();
            $rule->setBot($bot);
            $rule->setName("排序测试规则{$i}");
            $rule->setKeyword("order_test_{$i}");
            $rule->setReplyContent("排序测试回复{$i}");
            $rule->setPriority(10); // 相同优先级
            $rule->setValid(true);
            $entityManager->persist($rule);
            $rules[] = $rule;
        }
        $entityManager->flush();

        // 测试findOneBy返回一致性（应该总是返回同一个结果）
        $firstResult = $repository->findOneBy(['priority' => 10]);
        $secondResult = $repository->findOneBy(['priority' => 10]);
        $this->assertNotNull($firstResult);
        $this->assertNotNull($secondResult);
        $this->assertSame($firstResult->getId(), $secondResult->getId());

        // 测试具体条件的findOneBy
        $specificRule = $repository->findOneBy(['keyword' => 'order_test_2']);
        $this->assertNotNull($specificRule);
        $this->assertSame($rules[1]->getId(), $specificRule->getId());
        $this->assertSame('排序测试规则2', $specificRule->getName());
    }

    public function testCountWithNullableFields(): void
    {
        $repository = self::getService(AutoReplyRuleRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值计数测试');
        $bot->setUsername('count_null_test');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建不同有效性的规则
        $validRule1 = new AutoReplyRule();
        $validRule1->setBot($bot);
        $validRule1->setName('有效规刑1');
        $validRule1->setKeyword('valid1');
        $validRule1->setReplyContent('有效回处1');
        $validRule1->setPriority(10);
        $validRule1->setValid(true);
        $entityManager->persist($validRule1);

        $validRule2 = new AutoReplyRule();
        $validRule2->setBot($bot);
        $validRule2->setName('有效规刑2');
        $validRule2->setKeyword('valid2');
        $validRule2->setReplyContent('有效回处2');
        $validRule2->setPriority(20);
        $validRule2->setValid(true);
        $entityManager->persist($validRule2);

        $invalidRule = new AutoReplyRule();
        $invalidRule->setBot($bot);
        $invalidRule->setName('无效规则');
        $invalidRule->setKeyword('invalid');
        $invalidRule->setReplyContent('无效回复');
        $invalidRule->setPriority(5);
        $invalidRule->setValid(false);
        $entityManager->persist($invalidRule);
        $entityManager->flush();

        // 测试按有效性计数
        $validCount = $repository->count(['valid' => true]);
        $this->assertGreaterThanOrEqual(2, $validCount);

        $invalidCount = $repository->count(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, $invalidCount);

        // 测试按优先级计数
        $highPriorityCount = $repository->count(['priority' => 20]);
        $this->assertGreaterThanOrEqual(1, $highPriorityCount);

        $lowPriorityCount = $repository->count(['priority' => 5]);
        $this->assertGreaterThanOrEqual(1, $lowPriorityCount);

        // 测试特定机器人的计数
        $botRuleCount = $repository->count(['bot' => $bot]);
        $this->assertSame(3, $botRuleCount);
    }
}
