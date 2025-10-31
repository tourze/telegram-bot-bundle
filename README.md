# TelegramBotBundle

[![PHP Version Require](https://poser.pugx.org/tourze/telegram-bot-bundle/require/php)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![License](https://poser.pugx.org/tourze/telegram-bot-bundle/license)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![Latest Version](https://poser.pugx.org/tourze/telegram-bot-bundle/v)](https://packagist.org/packages/tourze/telegram-bot-bundle)
[![Build Status](https://github.com/tourze/php-monorepo/workflows/CI/badge.svg)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://codecov.io/gh/tourze/php-monorepo/branch/master/graph/badge.svg)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for managing Telegram bots, providing comprehensive bot management capabilities including:

- Bot creation, editing, and management
- Webhook message reception
- Auto-reply rule management
- Message processing and sending
- Command handling system

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Auto-Reply Rules](#auto-reply-rules)
- [Command Handling](#command-handling)
- [Event System](#event-system)
- [API](#api)
- [Advanced Usage](#advanced-usage)
- [License](#license)

## Features

### 🤖 Bot Management
- Create and manage multiple Telegram bots
- Set up webhook URLs automatically
- Bot validation and status tracking
- Comprehensive bot configuration options

### 📨 Message Processing
- Receive and process webhook messages
- Support for all Telegram message types
- Message persistence and tracking
- Real-time message handling

### 🎯 Auto-Reply System
- Rule-based automatic replies
- Exact and fuzzy text matching
- Priority-based rule processing
- HTML-formatted response support

### ⚡ Command System
- Extensible command handling framework
- Custom command handlers
- Argument parsing and validation
- Built-in system commands

### 📊 Analytics & Logging
- Command execution logging
- Message tracking and statistics
- User interaction analytics
- System monitoring capabilities

## Installation

### Requirements

- PHP 8.1 or higher  
- ext-json
- Symfony 7.3 or higher
- Doctrine ORM 3.0+
- Doctrine DBAL 4.0+
- EasyAdmin Bundle 4+

### Install via Composer

```bash
composer require tourze/telegram-bot-bundle
```

### Core Dependencies

- `doctrine/orm`: ^3.0
- `doctrine/dbal`: ^4.0  
- `doctrine/doctrine-bundle`: ^2.13
- `symfony/framework-bundle`: ^7.3
- `symfony/http-client-contracts`: ^3.6
- `easycorp/easyadmin-bundle`: ^4
- Plus additional Tourze bundles for enhanced functionality

## Quick Start

### 1. Bundle Registration

Register the bundle in your `config/bundles.php`:

```php
return [
    // ... other bundles
    TelegramBotBundle\TelegramBotBundle::class => ['all' => true],
];
```

### 2. Basic Configuration

The bundle works out-of-the-box without additional configuration files. The webhook endpoint is automatically available at:

```
POST /telegram/webhook/{bot-id}
```

For advanced configuration, you can customize services in your `config/services.yaml` if needed.

### 3. Usage

1. Run database migrations to create required tables:
```bash
bin/console doctrine:migrations:migrate
```

2. Access the EasyAdmin interface to manage bots:
    - Bots: `/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\TelegramBotCrudController`
    - Auto-Reply Rules: `/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\AutoReplyRuleCrudController`
    - Bot Commands: `/admin?crudAction=index&crudControllerFqcn=TelegramBotBundle\Controller\Admin\BotCommandCrudController`

3. Set up webhook URLs using the console command:
```bash
# Set webhook for a bot
bin/console telegram:set-webhook <bot-id> <base-url>

# Example:
bin/console telegram:set-webhook 123 https://your-domain.com
# This generates: https://your-domain.com/telegram/webhook/123
```

## Auto-Reply Rules

Create intelligent auto-reply rules through the admin interface:

- **Exact Matching**: Messages must match the keyword exactly
- **Fuzzy Matching**: Messages containing the keyword will trigger replies  
- **Priority System**: Higher priority rules (larger numbers) are processed first
- **HTML Support**: Reply content supports HTML formatting
- **Per-Bot Rules**: Each bot can have its own set of rules

## Command Handling

The bundle provides a command handling system that allows easy addition of new command handlers:

1. Create a command handler class:

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
        // Handle your command logic here
        $response = "Your reply message";
        $this->botService->sendMessage($bot, (string) $message->getChat()?->getId(), $response);
    }
}
```

2. Command handlers will be automatically registered and handle corresponding commands

## Event System

The bundle uses an event system to handle Webhook messages. You can create your own EventSubscriber to handle messages:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelegramBotBundle\Event\TelegramUpdateEvent;

class YourSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TelegramUpdateEvent::class => ['onTelegramUpdate', -10], // lower priority than auto-reply
        ];
    }

    public function onTelegramUpdate(TelegramUpdateEvent $event): void
    {
        $update = $event->getUpdate();
        $bot = $event->getBot();

        // Handle message...
    }
}
```

## API

### Sending Messages

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

## Advanced Usage

### Database Schema

The bundle creates several tables for managing bots and tracking activity:

- `tg_bot`: Bot configuration (name, token, webhook URL)
- `tg_auto_reply_rule`: Auto-reply rules with priority and matching options
- `telegram_bot_command`: Custom command definitions
- `telegram_update`: All received Telegram updates (with Snowflake IDs)
- `command_log`: Command execution audit trail

### Event-Driven Architecture

Monitor bot activity by subscribing to events:

### Custom Message Handlers

Create sophisticated message processing by implementing custom handlers:

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
        
        // Handle all types of updates (messages, edits, etc.)
        if ($messageText = $event->getMessageText()) {
            // Process text messages
        }
    }
    
    public function onTelegramCommand(TelegramCommandEvent $event): void
    {
        $command = $event->getCommand();
        $args = $event->getArgs();
        
        // Handle specific command executions
    }
}
```

### Custom Command Handlers

Implement the `CommandHandlerInterface` to create custom commands:

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
        $location = $args[0] ?? 'Unknown';
        $chatId = (string) $message->getChat()?->getId();
        
        $weatherInfo = $this->fetchWeatherData($location);
        $this->botService->sendMessage($bot, $chatId, $weatherInfo);
    }
    
    private function fetchWeatherData(string $location): string
    {
        // Implement your weather API integration
        return "Weather in {$location}: Sunny, 24°C";
    }
}
```

Register your handler in the admin interface by creating a `BotCommand` entry with your handler's FQCN.

## Contributing

We welcome contributions to improve this bundle! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes and add tests
4. Ensure all tests pass (`./vendor/bin/phpunit packages/telegram-bot-bundle/tests`)
5. Run static analysis (`php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/telegram-bot-bundle`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Setup

```bash
# Run tests
./vendor/bin/phpunit packages/telegram-bot-bundle/tests

# Run static analysis  
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/telegram-bot-bundle

# Check package integrity
bin/console app:check-packages telegram-bot-bundle -o -f
```

## Changelog

### Latest Changes
- Enhanced test coverage with comprehensive integration tests
- Improved PHPStan compliance and type safety
- Added support for all Telegram message types
- Implemented robust command parsing and logging
- Enhanced EasyAdmin integration for better management

### Version History
- **v0.1.x**: Core bot management and webhook handling
- **v0.2.x**: Auto-reply system and command framework  
- **v0.3.x**: Enhanced logging and event system
- **Current**: Full-featured bot development platform

## License

This bundle is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Reference Documentation

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Getting updates](https://core.telegram.org/bots/api#getting-updates)  
- [Available types](https://core.telegram.org/bots/api#available-types)
- [Available methods](https://core.telegram.org/bots/api#available-methods)
- [EasyAdmin Documentation](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)

