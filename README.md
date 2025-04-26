# TelegramBotBundle

这个 Bundle 提供了 Telegram 机器人的管理功能，包括:

- 机器人的添加、编辑和管理
- Webhook 接收消息
- 自动回复规则管理
- 消息处理与发送
- 命令处理系统

## 使用

1. 访问后台管理页面 `/admin/telegram-bot/bots` 添加机器人
2. 使用命令设置 Webhook URL:

```bash
# 设置单个机器人的 webhook
bin/console telegram:set-webhook --bot-id=123

# 使用自定义基础 URL
bin/console telegram:set-webhook --bot-id=123 --base-url=https://custom-domain.com
```

## 自动回复规则

- 支持精确匹配和模糊匹配
- 可以设置优先级，优先级高的规则会优先匹配
- 支持 HTML 格式的回复内容

## 命令处理

Bundle 提供了一个命令处理系统，可以轻松添加新的命令处理器：

1. 创建一个命令处理器类：

```php
use TelegramBotBundle\Event\TelegramCommandEvent;use TelegramBotBundle\Handler\AbstractCommandHandler;

class YourCommandHandler extends AbstractCommandHandler
{
    protected function getCommand(): string
    {
        return 'yourcommand'; // 对应 /yourcommand
    }

    protected function handle(TelegramCommandEvent $event): void
    {
        $args = $event->getArgs(); // 获取命令参数
        $this->reply($event, "你的回复消息");
    }
}
```

2. 命令处理器会自动注册并处理对应的命令

## 事件系统

Bundle 使用事件系统来处理 Webhook 消息，你可以创建自己的 EventSubscriber 来处理消息:

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

class YourService
{
    public function __construct(
        private readonly TelegramBotService $botService,
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

## 参考文档

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Getting updates](https://core.telegram.org/bots/api#getting-updates)
- [Available types](https://core.telegram.org/bots/api#available-types)
- [Available methods](https://core.telegram.org/bots/api#available-methods)
