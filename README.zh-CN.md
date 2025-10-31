# TelegramBotBundle

[![PHP Version Require](https://poser.pugx.org/tourze/telegram-bot-bundle/require/php)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![License](https://poser.pugx.org/tourze/telegram-bot-bundle/license)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![Latest Version](https://poser.pugx.org/tourze/telegram-bot-bundle/v)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![Build Status](https://github.com/tourze/php-monorepo/workflows/CI/badge.svg)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://codecov.io/gh/tourze/php-monorepo/branch/master/graph/badge.svg)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

一个用于管理 Telegram 机器人的 Symfony Bundle，提供全面的机器人管理功能，包括：

- 机器人的创建、编辑和管理
- Webhook 消息接收
- 自动回复规则管理
- 消息处理与发送
- 命令处理系统

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [快速开始](#快速开始)
- [自动回复规则](#自动回复规则)
- [命令处理](#命令处理)
- [事件系统](#事件系统)
- [API](#api)
- [高级用法](#高级用法)
- [许可证](#许可证)

## 功能特性

### 🤖 机器人管理
- 创建和管理多个 Telegram 机器人
- 自动设置 webhook URL
- 机器人验证和状态跟踪
- 全面的机器人配置选项

### 📨 消息处理
- 接收和处理 webhook 消息
- 支持所有 Telegram 消息类型
- 消息持久化和跟踪
- 实时消息处理

### 🎯 自动回复系统
- 基于规则的自动回复
- 精确和模糊文本匹配
- 基于优先级的规则处理
- HTML 格式回复支持

### ⚡ 命令系统
- 可扩展的命令处理框架
- 自定义命令处理器
- 参数解析和验证
- 内置系统命令

### 📊 分析和日志
- 命令执行日志
- 消息跟踪和统计
- 用户交互分析
- 系统监控功能

## 安装

### 系统要求

- PHP 8.1 或更高版本
- ext-json  
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0+
- Doctrine DBAL 4.0+
- EasyAdmin Bundle 4+

### 通过 Composer 安装

```bash
composer require tourze/telegram-bot-bundle
```

### 核心依赖

- `doctrine/orm`: ^3.0
- `doctrine/dbal`: ^4.0
- `doctrine/doctrine-bundle`: ^2.13  
- `symfony/framework-bundle`: ^7.3
- `symfony/http-client-contracts`: ^3.6
- `easycorp/easyadmin-bundle`: ^4
- 以及额外的 Tourze 扩展包提供增强功能

## 快速开始

### 1. Bundle 注册

在你的 `config/bundles.php` 中注册 bundle：

```php
return [
    // ... 其他 bundles
    TelegramBotBundle\TelegramBotBundle::class => ['all' => true],
];
```

### 2. 基本配置

Bundle 开箱即用，无需额外配置文件。Webhook 端点自动可用：

```
POST /telegram/webhook/{bot-id}
```

如需高级配置，可在 `config/services.yaml` 中自定义服务。

### 3. 使用方法

1. 运行数据库迁移创建必要的表：
```bash
bin/console doctrine:migrations:migrate
```

2. 通过 EasyAdmin 界面管理机器人：
    - 机器人管理：`/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\TelegramBotCrudController`
    - 自动回复规则：`/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\AutoReplyRuleCrudController`
    - 机器人命令：`/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\BotCommandCrudController`

3. 使用控制台命令设置 Webhook URL：
```bash
# 为机器人设置 webhook
bin/console telegram:set-webhook <机器人ID> <基础URL>

# 示例：
bin/console telegram:set-webhook 123 https://your-domain.com
# 生成：https://your-domain.com/telegram/webhook/123
```

## 自动回复规则

通过管理界面创建智能自动回复规则：

- **精确匹配**：消息必须完全匹配关键词
- **模糊匹配**：包含关键词的消息将触发回复
- **优先级系统**：数值更大的优先级规则优先处理
- **HTML 支持**：回复内容支持 HTML 格式
- **机器人独立**：每个机器人可拥有独立的规则集

## 命令处理

Bundle 提供了一个命令处理系统，可以轻松添加新的命令处理器：

1. 创建一个命令处理器类：

```php
use TelegramBotBundle\Handler\CommandHandlerInterface;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Service\TelegramBotService;

class YourCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TelegramBotService $botService,
    ) {
    }

    public function handle(TelegramBot $bot, string $command, array $args, TelegramMessage $message): void
    {
        // 在这里处理你的命令逻辑
        $response = "你的回复消息";
        $this->botService->sendMessage($bot, (string) $message->getChat()?->getId(), $response);
    }
}
```

2. 命令处理器会自动注册并处理对应的命令

## 事件系统

Bundle 使用事件系统来处理 Webhook 消息，你可以创建自己的 EventSubscriber 来处理消息：

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelegramBotBundle\Event\TelegramUpdateEvent;

class YourSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TelegramUpdateEvent::class => ['onTelegramUpdate', -10], // 优先级低于自动回复
        ];
    }

    public function onTelegramUpdate(TelegramUpdateEvent $event): void
    {
        $update = $event->getUpdate();
        $bot = $event->getBot();

        // 处理消息...
    }
}
```

## API

### 发送消息

```php
use TelegramBotBundle\Service\TelegramBotService;
use TelegramBotBundle\Repository\TelegramBotRepository;

class YourService
{
    public function __construct(
        private readonly TelegramBotService $botService,
        private readonly TelegramBotRepository $botRepository,
    ) {
    }

    public function sendMessage(string $botId, string $chatId, string $message): void
    {
        $bot = $this->botRepository->find($botId);
        if ($bot) {
            $this->botService->sendMessage($bot, $chatId, $message);
        }
    }
}
```

## 高级用法

### 数据库架构

Bundle 创建多个表来管理机器人和跟踪活动：

- `tg_bot`：机器人配置（名称、令牌、Webhook URL）
- `tg_auto_reply_rule`：自动回复规则与优先级、匹配选项
- `telegram_bot_command`：自定义命令定义
- `telegram_update`：所有接收到的 Telegram 更新（使用 Snowflake ID）
- `command_log`：命令执行审计跟踪

### 事件驱动架构

通过订阅事件监控机器人活动：

### 自定义消息处理器

通过实现自定义处理器创建复杂的消息处理逻辑：

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use TelegramBotBundle\Event\TelegramCommandEvent;

class CustomBotSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TelegramUpdateEvent::class => ['onTelegramUpdate', -10],
            TelegramCommandEvent::class => ['onTelegramCommand', 0],
        ];
    }

    public function onTelegramUpdate(TelegramUpdateEvent $event): void
    {
        $update = $event->getUpdate();
        $bot = $event->getBot();
        
        // 处理所有类型的更新（消息、编辑等）
        if ($messageText = $event->getMessageText()) {
            // 处理文本消息
        }
    }
    
    public function onTelegramCommand(TelegramCommandEvent $event): void
    {
        $command = $event->getCommand();
        $args = $event->getArgs();
        
        // 处理特定的命令执行
    }
}
```

### 自定义命令处理器

实现 `CommandHandlerInterface` 创建自定义命令：

```php
use TelegramBotBundle\Handler\CommandHandlerInterface;
use TelegramBotBundle\Entity\TelegramBot;
use TelegramBotBundle\Entity\Embeddable\TelegramMessage;
use TelegramBotBundle\Service\TelegramBotService;

class WeatherCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TelegramBotService $botService,
    ) {
    }

    public function handle(TelegramBot $bot, string $command, array $args, TelegramMessage $message): void
    {
        $location = $args[0] ?? '未知';
        $chatId = (string) $message->getChat()?->getId();
        
        $weatherInfo = $this->fetchWeatherData($location);
        $this->botService->sendMessage($bot, $chatId, $weatherInfo);
    }
    
    private function fetchWeatherData(string $location): string
    {
        // 实现你的天气 API 集成
        return "天气信息 {$location}：晴天，24°C";
    }
}
```

通过在管理界面创建 `BotCommand` 条目并设置处理器的完整类名来注册你的处理器。

## 贡献

我们欢迎为改进这个 Bundle 做出贡献！请：

1. Fork 仓库
2. 创建特性分支 (`git checkout -b feature/amazing-feature`)
3. 进行更改并添加测试
4. 确保所有测试通过 (`./vendor/bin/phpunit packages/telegram-bot-bundle/tests`)
5. 运行静态分析 (`php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/telegram-bot-bundle`)
6. 提交更改 (`git commit -m 'Add amazing feature'`)
7. 推送到分支 (`git push origin feature/amazing-feature`)
8. 创建 Pull Request

### 开发环境设置

```bash
# 运行测试
./vendor/bin/phpunit packages/telegram-bot-bundle/tests

# 运行静态分析  
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/telegram-bot-bundle

# 检查包完整性
bin/console app:check-packages telegram-bot-bundle -o -f
```

## 更新日志

### 最新更改
- 增强测试覆盖率，提供全面的集成测试
- 改进 PHPStan 合规性和类型安全性
- 添加对所有 Telegram 消息类型的支持
- 实现强大的命令解析和日志记录
- 增强 EasyAdmin 集成以实现更好的管理

### 版本历史
- **v0.1.x**：核心机器人管理和 Webhook 处理
- **v0.2.x**：自动回复系统和命令框架
- **v0.3.x**：增强的日志记录和事件系统
- **当前版本**：功能完整的机器人开发平台

## 许可证

此 Bundle 使用 MIT 许可证。详情请参见 [LICENSE](LICENSE) 文件。

## 参考文档

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Getting updates](https://core.telegram.org/bots/api#getting-updates)  
- [Available types](https://core.telegram.org/bots/api#available-types)
- [Available methods](https://core.telegram.org/bots/api#available-methods)
- [EasyAdmin 文档](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)

