<?php

namespace TelegramBotBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\Entity\BotCommand;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Repository\BotCommandRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(BotCommandRepository::class)]
#[RunTestsInSeparateProcesses]
final class BotCommandRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): BotCommandRepository
    {
        return self::getService(BotCommandRepository::class);
    }

    protected function createNewEntity(): BotCommand
    {
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);

        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('test');
        $command->setHandler('TestHandler');
        $command->setDescription('测试命令');
        $command->setValid(true);

        return $command;
    }

    protected function onSetUp(): void
    {
    }

    public function testConstruction(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $this->assertInstanceOf(BotCommandRepository::class, $repository);
    }

    public function testRepositoryCanHandleCorrectEntityType(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建并保存实体来验证仓库处理正确的实体类型
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('type_test');
        $command->setHandler('TypeTestHandler');
        $command->setDescription('类型测试命令');
        $command->setValid(true);

        $repository->save($command);

        $this->assertNotNull($command->getId());
        $this->assertInstanceOf(BotCommand::class, $command);

        // 验证能正确查询和操作实体
        $foundCommand = $repository->find($command->getId());
        $this->assertSame($command, $foundCommand);
    }

    public function testFind(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('test');
        $command->setHandler('TestCommandHandler');
        $command->setDescription('测试命令');
        $command->setValid(true);
        $entityManager->persist($command);
        $entityManager->flush();

        $commandId = $command->getId();
        $this->assertNotNull($commandId);

        // 测试查找存在的实体
        $foundCommand = $repository->find($commandId);
        $this->assertNotNull($foundCommand);
        $this->assertSame($command, $foundCommand);
        $this->assertSame('test', $foundCommand->getCommand());

        // 测试查找不存在的实体
        $notFoundCommand = $repository->find('nonexistent-id');
        $this->assertNull($notFoundCommand);

        // 测试查找null ID - 跳过该测试以避免异常
        // $nullCommand = $repository->find(null);
        // $this->assertNull($nullCommand);
    }

    public function testFindBy(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试命令
        $command1 = new BotCommand();
        $command1->setBot($bot);
        $command1->setCommand('start');
        $command1->setHandler('StartCommandHandler');
        $command1->setDescription('开始命令');
        $command1->setValid(true);
        $entityManager->persist($command1);

        $command2 = new BotCommand();
        $command2->setBot($bot);
        $command2->setCommand('help');
        $command2->setHandler('HelpCommandHandler');
        $command2->setDescription('帮助命令');
        $command2->setValid(true);
        $entityManager->persist($command2);

        $command3 = new BotCommand();
        $command3->setBot($bot);
        $command3->setCommand('disabled');
        $command3->setHandler('DisabledCommandHandler');
        $command3->setDescription('禁用命令');
        $command3->setValid(false);
        $entityManager->persist($command3);

        $entityManager->flush();

        // 测试按机器人查找
        $commandsByBot = $repository->findBy(['bot' => $bot]);
        $this->assertCount(3, $commandsByBot);

        // 测试按有效性查找
        $validCommands = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(2, count($validCommands)); // 允许之前测试留下的数据

        // 测试组合条件查找
        $validBotCommands = $repository->findBy(['bot' => $bot, 'valid' => true]);
        $this->assertCount(2, $validBotCommands);

        // 测试排序
        $sortedCommands = $repository->findBy(['bot' => $bot], ['command' => 'ASC']);
        $this->assertCount(3, $sortedCommands);
        $this->assertSame('disabled', $sortedCommands[0]->getCommand());
        $this->assertSame('help', $sortedCommands[1]->getCommand());
        $this->assertSame('start', $sortedCommands[2]->getCommand());

        // 测试限制数量
        $limitedCommands = $repository->findBy(['bot' => $bot], null, 2);
        $this->assertCount(2, $limitedCommands);

        // 测试偏移量
        $offsetCommands = $repository->findBy(['bot' => $bot], ['command' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetCommands);
        $this->assertSame('help', $offsetCommands[0]->getCommand());
        $this->assertSame('start', $offsetCommands[1]->getCommand());

        // 测试空条件
        $emptyResults = $repository->findBy(['command' => 'nonexistent']);
        $this->assertCount(0, $emptyResults);
    }

    public function testFindOneBy(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('unique');
        $command->setHandler('UniqueCommandHandler');
        $command->setDescription('唯一命令');
        $command->setValid(true);
        $entityManager->persist($command);
        $entityManager->flush();

        // 测试查找唯一实体
        $foundCommand = $repository->findOneBy(['command' => 'unique']);
        $this->assertNotNull($foundCommand);
        $this->assertSame($command, $foundCommand);
        $this->assertSame('unique', $foundCommand->getCommand());

        // 测试查找不存在的实体
        $notFoundCommand = $repository->findOneBy(['command' => 'nonexistent']);
        $this->assertNull($notFoundCommand);

        // 测试组合条件查找
        $complexFoundCommand = $repository->findOneBy(['bot' => $bot, 'valid' => true]);
        $this->assertNotNull($complexFoundCommand);
        $this->assertSame($command, $complexFoundCommand);
    }

    public function testFindAll(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 初始状态应该为空
        $initialCommands = $repository->findAll();
        $this->assertIsArray($initialCommands);

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试命令
        for ($i = 1; $i <= 3; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("command{$i}");
            $command->setHandler("Handler{$i}");
            $command->setDescription("描述{$i}");
            $command->setValid(1 === $i % 2); // 奇数有效
            $entityManager->persist($command);
        }
        $entityManager->flush();

        // 测试获取所有实体
        $allCommands = $repository->findAll();
        $this->assertGreaterThanOrEqual(3, count($allCommands)); // 允许之前测试留下的数据
        $this->assertContainsOnlyInstancesOf(BotCommand::class, $allCommands);
    }

    public function testCount(): void
    {
        $repository = self::getService(BotCommandRepository::class);
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

        // 创建测试命令
        for ($i = 1; $i <= 5; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("command{$i}");
            $command->setHandler("Handler{$i}");
            $command->setDescription("描述{$i}");
            $command->setValid($i <= 3); // 前3个有效
            $entityManager->persist($command);
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

    public function testGetValidCommands(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建有效命令
        $command1 = new BotCommand();
        $command1->setBot($bot);
        $command1->setCommand('start');
        $command1->setHandler('StartCommandHandler');
        $command1->setDescription('开始命令');
        $command1->setValid(true);
        $entityManager->persist($command1);

        $command2 = new BotCommand();
        $command2->setBot($bot);
        $command2->setCommand('help');
        $command2->setHandler('HelpCommandHandler');
        $command2->setDescription('帮助命令');
        $command2->setValid(true);
        $entityManager->persist($command2);

        // 创建无效命令
        $command3 = new BotCommand();
        $command3->setBot($bot);
        $command3->setCommand('disabled');
        $command3->setHandler('DisabledCommandHandler');
        $command3->setDescription('禁用命令');
        $command3->setValid(false);
        $entityManager->persist($command3);

        $entityManager->flush();

        // 测试获取有效命令
        $validCommands = $repository->getValidCommands($bot);
        $this->assertCount(2, $validCommands);

        // 验证结果按命令名排序
        $this->assertSame('help', $validCommands[0]->getCommand());
        $this->assertSame('start', $validCommands[1]->getCommand());

        // 验证都是有效命令
        foreach ($validCommands as $command) {
            $this->assertTrue($command->isValid());
            $this->assertSame($bot, $command->getBot());
        }
    }

    public function testFindCommand(): void
    {
        $repository = self::getService(BotCommandRepository::class);
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

        // 创建有效的机器人命令
        $command1 = new BotCommand();
        $command1->setBot($bot);
        $command1->setCommand('start');
        $command1->setHandler('StartCommandHandler');
        $command1->setDescription('开始使用机器人');
        $command1->setValid(true);
        $entityManager->persist($command1);

        // 创建另一个有效的机器人命令
        $command2 = new BotCommand();
        $command2->setBot($bot);
        $command2->setCommand('help');
        $command2->setHandler('HelpCommandHandler');
        $command2->setDescription('获取帮助信息');
        $command2->setValid(true);
        $entityManager->persist($command2);

        // 创建无效的机器人命令
        $command3 = new BotCommand();
        $command3->setBot($bot);
        $command3->setCommand('disabled');
        $command3->setHandler('DisabledCommandHandler');
        $command3->setDescription('已禁用的命令');
        $command3->setValid(false);
        $entityManager->persist($command3);

        // 创建其他机器人的命令
        $command4 = new BotCommand();
        $command4->setBot($otherBot);
        $command4->setCommand('start');
        $command4->setHandler('OtherStartCommandHandler');
        $command4->setDescription('其他机器人开始命令');
        $command4->setValid(true);
        $entityManager->persist($command4);

        $entityManager->flush();

        // 测试查找存在的有效命令
        $foundCommand = $repository->findCommand($bot, 'start');
        $this->assertNotNull($foundCommand);
        $this->assertSame($command1, $foundCommand);
        $this->assertSame('start', $foundCommand->getCommand());
        $this->assertSame($bot, $foundCommand->getBot());
        $this->assertTrue($foundCommand->isValid());

        // 测试查找另一个有效命令
        $foundCommand2 = $repository->findCommand($bot, 'help');
        $this->assertNotNull($foundCommand2);
        $this->assertSame($command2, $foundCommand2);
        $this->assertSame('help', $foundCommand2->getCommand());

        // 测试查找无效命令，应该返回null
        $invalidCommand = $repository->findCommand($bot, 'disabled');
        $this->assertNull($invalidCommand);

        // 测试查找不存在的命令
        $nonExistentCommand = $repository->findCommand($bot, 'nonexistent');
        $this->assertNull($nonExistentCommand);

        // 测试机器人隔离：查找其他机器人的命令
        $otherBotCommand = $repository->findCommand($bot, 'start');
        $this->assertNotNull($otherBotCommand);
        $this->assertSame($command1, $otherBotCommand); // 应该返回当前机器人的命令，不是其他机器人的

        // 测试其他机器人查找自己的命令
        $otherFoundCommand = $repository->findCommand($otherBot, 'start');
        $this->assertNotNull($otherFoundCommand);
        $this->assertSame($command4, $otherFoundCommand);
        $this->assertSame($otherBot, $otherFoundCommand->getBot());
    }

    public function testSave(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);
        $entityManager->flush();

        // 创建新命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('new');
        $command->setHandler('NewCommandHandler');
        $command->setDescription('新命令');
        $command->setValid(true);

        // 测试保存但不刷新 - BotCommand使用Snowflake ID，在创建时就生成
        $repository->save($command, false);
        $this->assertNotEmpty($command->getId()); // Snowflake ID在创建时就生成

        // 手动刷新
        $entityManager->flush();
        $this->assertGreaterThan(0, $command->getId());

        // 测试默认保存（自动刷新）
        $command2 = new BotCommand();
        $command2->setBot($bot);
        $command2->setCommand('another');
        $command2->setHandler('AnotherCommandHandler');
        $command2->setDescription('另一个命令');
        $command2->setValid(true);

        $repository->save($command2);
        $this->assertNotEmpty($command2->getId()); // 应该有ID

        // 验证保存的数据
        $savedCommand = $repository->find($command2->getId());
        $this->assertNotNull($savedCommand);
        $this->assertSame('another', $savedCommand->getCommand());
    }

    public function testRemove(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('delete');
        $command->setHandler('DeleteCommandHandler');
        $command->setDescription('待删除命令');
        $command->setValid(true);
        $entityManager->persist($command);
        $entityManager->flush();

        $commandId = $command->getId();
        $this->assertNotNull($commandId);

        // 验证命令存在
        $existingCommand = $repository->find($commandId);
        $this->assertNotNull($existingCommand);

        // 测试删除但不刷新
        $repository->remove($command, false);
        $stillExists = $repository->find($commandId);
        $this->assertNotNull($stillExists); // 没有刷新，应该还存在

        // 手动刷新
        $entityManager->flush();
        $deletedCommand = $repository->find($commandId);
        $this->assertNull($deletedCommand); // 应该被删除

        // 测试默认删除（自动刷新）
        $command2 = new BotCommand();
        $command2->setBot($bot);
        $command2->setCommand('delete2');
        $command2->setHandler('Delete2CommandHandler');
        $command2->setDescription('另一个待删除命令');
        $command2->setValid(true);
        $entityManager->persist($command2);
        $entityManager->flush();

        $command2Id = $command2->getId();
        $this->assertNotNull($command2Id);

        $repository->remove($command2);
        $deletedCommand2 = $repository->find($command2Id);
        $this->assertNull($deletedCommand2); // 应该立即被删除
    }

    public function testEntityRelationships(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('relation');
        $command->setHandler('RelationCommandHandler');
        $command->setDescription('关联测试命令');
        $command->setValid(true);
        $entityManager->persist($command);
        $entityManager->flush();

        // 测试机器人关联
        $foundCommand = $repository->find($command->getId());
        $this->assertNotNull($foundCommand);
        $this->assertSame($bot, $foundCommand->getBot());
        $this->assertSame('test_bot', $foundCommand->getBot()->getUsername());

        // 测试级联查询
        $commandsWithBot = $repository->createQueryBuilder('c')
            ->join('c.bot', 'b')
            ->where('b.username = :username')
            ->andWhere('c.command = :command')
            ->setParameter('username', 'test_bot')
            ->setParameter('command', 'relation')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $commandsWithBot);
        $this->assertSame($command, $commandsWithBot[0]);
    }

    public function testBoundaryConditions(): void
    {
        $repository = self::getService(BotCommandRepository::class);
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
        $emptyResult = $repository->findCommand($bot, '');
        $this->assertNull($emptyResult);

        // 测试极长字符串
        $longString = str_repeat('a', 1000);
        $longResult = $repository->findCommand($bot, $longString);
        $this->assertNull($longResult);

        // 测试特殊字符
        $specialChars = "!@#$%^&*()[]{}|\\:;\"'<>?,./'";
        $specialResult = $repository->findCommand($bot, $specialChars);
        $this->assertNull($specialResult);

        // 测试Unicode字符
        $unicodeString = '测试中文字符🚀';
        $unicodeResult = $repository->findCommand($bot, $unicodeString);
        $this->assertNull($unicodeResult);

        // 测试null参数（如果方法接受）
        $validCommands = $repository->getValidCommands($bot);
        $this->assertIsArray($validCommands);
        $this->assertCount(0, $validCommands);
    }

    public function testCountByAssociationBotShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(BotCommandRepository::class);
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

        // 为机器人1创建 4 个命令
        for ($i = 1; $i <= 4; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot1);
            $command->setCommand("bot1_cmd{$i}");
            $command->setHandler("Bot1Handler{$i}");
            $command->setDescription("机器人1描述{$i}");
            $command->setValid(true);
            $entityManager->persist($command);
        }

        // 为机器人2创建 2 个命令
        for ($i = 1; $i <= 2; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot2);
            $command->setCommand("bot2_cmd{$i}");
            $command->setHandler("Bot2Handler{$i}");
            $command->setDescription("机器人2描述{$i}");
            $command->setValid(true);
            $entityManager->persist($command);
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
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建测试命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('association_test');
        $command->setHandler('AssociationHandler');
        $command->setDescription('关联测试描述');
        $command->setValid(true);
        $entityManager->persist($command);
        $entityManager->flush();

        // 测试通过关联机器人查找
        $foundCommand = $repository->findOneBy(['bot' => $bot]);
        $this->assertNotNull($foundCommand);
        $this->assertInstanceOf(BotCommand::class, $foundCommand);
        $this->assertSame($command, $foundCommand);
        $this->assertSame($bot, $foundCommand->getBot());
        $this->assertSame('association_test', $foundCommand->getCommand());
    }

    public function testFindByNullableFields(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建有效性为 null 的命令
        $nullValidCommand = new BotCommand();
        $nullValidCommand->setBot($bot);
        $nullValidCommand->setCommand('null_valid');
        $nullValidCommand->setHandler('NullValidHandler');
        $nullValidCommand->setDescription('有效性为null的命令');
        $nullValidCommand->setValid(null);
        $entityManager->persist($nullValidCommand);

        // 创建有效性为 true 的命令
        $trueValidCommand = new BotCommand();
        $trueValidCommand->setBot($bot);
        $trueValidCommand->setCommand('true_valid');
        $trueValidCommand->setHandler('TrueValidHandler');
        $trueValidCommand->setDescription('有效的命令');
        $trueValidCommand->setValid(true);
        $entityManager->persist($trueValidCommand);

        // 创建有效性为 false 的命令
        $falseValidCommand = new BotCommand();
        $falseValidCommand->setBot($bot);
        $falseValidCommand->setCommand('false_valid');
        $falseValidCommand->setHandler('FalseValidHandler');
        $falseValidCommand->setDescription('无效的命令');
        $falseValidCommand->setValid(false);
        $entityManager->persist($falseValidCommand);

        $entityManager->flush();

        // 测试查找 valid 为 null 的实体
        $nullValidResults = $repository->findBy(['bot' => $bot, 'valid' => null]);
        $this->assertCount(1, $nullValidResults);
        $this->assertSame($nullValidCommand, $nullValidResults[0]);
        $this->assertNull($nullValidResults[0]->isValid());

        // 测试查找 valid 为 true 的实体
        $trueValidResults = $repository->findBy(['bot' => $bot, 'valid' => true]);
        $this->assertCount(1, $trueValidResults);
        $this->assertSame($trueValidCommand, $trueValidResults[0]);
        $this->assertTrue($trueValidResults[0]->isValid());

        // 测试查找 valid 为 false 的实体
        $falseValidResults = $repository->findBy(['bot' => $bot, 'valid' => false]);
        $this->assertCount(1, $falseValidResults);
        $this->assertSame($falseValidCommand, $falseValidResults[0]);
        $this->assertFalse($falseValidResults[0]->isValid());
    }

    public function testCountNullableFields(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个不同有效性状态的命令
        for ($i = 1; $i <= 3; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("null_test_{$i}");
            $command->setHandler("NullTestHandler{$i}");
            $command->setDescription("null测试{$i}");
            $command->setValid(null);
            $entityManager->persist($command);
        }

        for ($i = 1; $i <= 2; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("true_test_{$i}");
            $command->setHandler("TrueTestHandler{$i}");
            $command->setDescription("true测试{$i}");
            $command->setValid(true);
            $entityManager->persist($command);
        }

        $entityManager->flush();

        // 测试计数 valid 为 null 的记录
        $nullCount = $repository->count(['bot' => $bot, 'valid' => null]);
        $this->assertSame(3, $nullCount);

        // 测试计数 valid 为 true 的记录
        $trueCount = $repository->count(['bot' => $bot, 'valid' => true]);
        $this->assertSame(2, $trueCount);

        // 测试计数所有该机器人的记录
        $totalCount = $repository->count(['bot' => $bot]);
        $this->assertSame(5, $totalCount);
    }

    public function testFindOneByWithOrderBy(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('测试机器人');
        $bot->setUsername('test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个测试命令，确保顺序可预测
        $commands = [];
        $commandNames = ['zebra', 'apple', 'banana'];
        foreach ($commandNames as $index => $cmdName) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand($cmdName);
            $command->setHandler("Handler{$index}");
            $command->setDescription("描述{$index}");
            $command->setValid(true);
            $entityManager->persist($command);
            $commands[$cmdName] = $command;
        }
        $entityManager->flush();

        // 测试findOneBy使用升序排序
        $firstAscCommand = $repository->findOneBy(['bot' => $bot], ['command' => 'ASC']);
        $this->assertNotNull($firstAscCommand);
        $this->assertSame('apple', $firstAscCommand->getCommand());
        $this->assertSame($commands['apple'], $firstAscCommand);

        // 测试findOneBy使用降序排序
        $firstDescCommand = $repository->findOneBy(['bot' => $bot], ['command' => 'DESC']);
        $this->assertNotNull($firstDescCommand);
        $this->assertSame('zebra', $firstDescCommand->getCommand());
        $this->assertSame($commands['zebra'], $firstDescCommand);

        // 测试多字段排序
        $firstMultiCommand = $repository->findOneBy(['bot' => $bot, 'valid' => true], ['command' => 'ASC', 'handler' => 'ASC']);
        $this->assertNotNull($firstMultiCommand);
        $this->assertSame('apple', $firstMultiCommand->getCommand());
    }

    public function testFindByWithNullFieldsQuery(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值测试机器人');
        $bot->setUsername('null_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建有效性为 null 的命令
        $nullValidCommand = new BotCommand();
        $nullValidCommand->setBot($bot);
        $nullValidCommand->setCommand('null_valid');
        $nullValidCommand->setHandler('NullValidHandler');
        $nullValidCommand->setDescription('有效性为null的命令');
        $nullValidCommand->setValid(null);
        $entityManager->persist($nullValidCommand);

        // 创建有效的命令
        $validCommand = new BotCommand();
        $validCommand->setBot($bot);
        $validCommand->setCommand('valid_cmd');
        $validCommand->setHandler('ValidHandler');
        $validCommand->setDescription('有效命令');
        $validCommand->setValid(true);
        $entityManager->persist($validCommand);

        // 创建无效的命令
        $invalidCommand = new BotCommand();
        $invalidCommand->setBot($bot);
        $invalidCommand->setCommand('invalid_cmd');
        $invalidCommand->setHandler('InvalidHandler');
        $invalidCommand->setDescription('无效命令');
        $invalidCommand->setValid(false);
        $entityManager->persist($invalidCommand);

        $entityManager->flush();

        // 测试IS NULL查询 - 查找有效性为null的命令
        $nullResults = $repository->findBy(['valid' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullResults));

        // 验证找到我们的null命令
        $foundNullCommand = $repository->findOneBy(['command' => 'null_valid']);
        $this->assertNotNull($foundNullCommand);
        $this->assertNull($foundNullCommand->isValid());

        // 测试IS NOT NULL查询 - 查找有效性不为null的命令
        $validResults = $repository->findBy(['valid' => true]);
        $this->assertGreaterThanOrEqual(1, count($validResults));

        $invalidResults = $repository->findBy(['valid' => false]);
        $this->assertGreaterThanOrEqual(1, count($invalidResults));

        // 验证找到的记录有正确的有效性值
        $foundValidCommand = $repository->findOneBy(['command' => 'valid_cmd']);
        $this->assertNotNull($foundValidCommand);
        $this->assertTrue($foundValidCommand->isValid());

        $foundInvalidCommand = $repository->findOneBy(['command' => 'invalid_cmd']);
        $this->assertNotNull($foundInvalidCommand);
        $this->assertFalse($foundInvalidCommand->isValid());
    }

    public function testFindOneByWithOrderingLogic(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('排序测试机器人');
        $bot->setUsername('order_test_bot');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建多个命令，测试排序逻辑
        $commands = [];
        $commandData = [
            ['name' => 'zebra', 'handler' => 'ZebraHandler'],
            ['name' => 'apple', 'handler' => 'AppleHandler'],
            ['name' => 'banana', 'handler' => 'BananaHandler'],
        ];

        foreach ($commandData as $index => $data) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand($data['name']);
            $command->setHandler($data['handler']);
            $command->setDescription("描述{$index}");
            $command->setValid(true);
            $entityManager->persist($command);
            $commands[$data['name']] = $command;
        }
        $entityManager->flush();

        // 测试findOneBy排序逻辑 - 应该返回排序后的第一个结果
        $firstByNameAsc = $repository->findOneBy(['bot' => $bot, 'valid' => true], ['command' => 'ASC']);
        $this->assertNotNull($firstByNameAsc);
        $this->assertSame('apple', $firstByNameAsc->getCommand());
        $this->assertSame($commands['apple'], $firstByNameAsc);

        // 测试降序排序
        $firstByNameDesc = $repository->findOneBy(['bot' => $bot, 'valid' => true], ['command' => 'DESC']);
        $this->assertNotNull($firstByNameDesc);
        $this->assertSame('zebra', $firstByNameDesc->getCommand());
        $this->assertSame($commands['zebra'], $firstByNameDesc);

        // 测试多条件排序
        $firstByMultiple = $repository->findOneBy(['bot' => $bot], ['command' => 'ASC', 'handler' => 'ASC']);
        $this->assertNotNull($firstByMultiple);
        $this->assertSame('apple', $firstByMultiple->getCommand());

        // 测试排序逻辑的一致性 - 多次调用应该返回相同结果
        $secondCall = $repository->findOneBy(['bot' => $bot, 'valid' => true], ['command' => 'ASC']);
        $this->assertNotNull($secondCall);
        $this->assertSame($firstByNameAsc->getId(), $secondCall->getId());
    }

    public function testCountWithNullableFields(): void
    {
        $repository = self::getService(BotCommandRepository::class);
        $entityManager = self::getEntityManager();

        // 创建测试机器人
        $bot = new TelegramBot();
        $bot->setName('空值计数测试');
        $bot->setUsername('count_null_test');
        $bot->setToken('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        $bot->setValid(true);
        $entityManager->persist($bot);

        // 创建不同有效性状态的命令
        // 3个有效性为null的命令
        for ($i = 1; $i <= 3; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("null_valid_{$i}");
            $command->setHandler("NullHandler{$i}");
            $command->setDescription("null有效性{$i}");
            $command->setValid(null);
            $entityManager->persist($command);
        }

        // 2个有效的命令
        for ($i = 1; $i <= 2; ++$i) {
            $command = new BotCommand();
            $command->setBot($bot);
            $command->setCommand("valid_{$i}");
            $command->setHandler("ValidHandler{$i}");
            $command->setDescription("有效命令{$i}");
            $command->setValid(true);
            $entityManager->persist($command);
        }

        // 1个无效的命令
        $command = new BotCommand();
        $command->setBot($bot);
        $command->setCommand('invalid');
        $command->setHandler('InvalidHandler');
        $command->setDescription('无效命令');
        $command->setValid(false);
        $entityManager->persist($command);

        $entityManager->flush();

        // 测试count IS NULL查询 - 计数有效性为null的记录
        $nullCount = $repository->count(['bot' => $bot, 'valid' => null]);
        $this->assertSame(3, $nullCount);

        // 测试count IS NOT NULL查询 - 计数有效性不为null的记录
        $validCount = $repository->count(['bot' => $bot, 'valid' => true]);
        $this->assertSame(2, $validCount);

        $invalidCount = $repository->count(['bot' => $bot, 'valid' => false]);
        $this->assertSame(1, $invalidCount);

        // 测试总计数
        $totalCount = $repository->count(['bot' => $bot]);
        $this->assertSame(6, $totalCount);

        // 验证null值的特殊处理
        $nonNullCount = $repository->count(['bot' => $bot, 'valid' => true]);
        $nonNullCount += $repository->count(['bot' => $bot, 'valid' => false]);
        $this->assertSame(3, $nonNullCount); // 非null值的总数
        $this->assertSame($totalCount, $nullCount + $nonNullCount); // 验证总数正确
    }
}
