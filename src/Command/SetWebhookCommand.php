<?php

namespace TelegramBotBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TelegramBotBundle\Repository\TelegramBotRepository;
use TelegramBotBundle\Service\TelegramBotService;

/**
 * 设置 Telegram Bot 的 Webhook URL
 *
 * 参考文档: https://core.telegram.org/bots/api#setwebhook
 */
#[AsCommand(name: self::NAME, description: '设置 Telegram Bot 的 Webhook URL')]
class SetWebhookCommand extends Command
{
    public const NAME = 'telegram:set-webhook';

    public function __construct(
        private readonly TelegramBotRepository $botRepository,
        private readonly TelegramBotService $botService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('bot-id', InputOption::VALUE_REQUIRED, '机器人ID')
            ->addArgument('base-url', InputOption::VALUE_REQUIRED, '基础URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $botId = $input->getArgument('bot-id');
        $baseUrl = $input->getArgument('base-url');
        if (empty($baseUrl)) {
            $io->error('基础URL不能为空');

            return Command::FAILURE;
        }

        $bot = $this->botRepository->find($botId);
        if ($bot === null) {
            $io->error('Bot not found');

            return Command::FAILURE;
        }

        // 生成 webhook URL
        $webhookUrl = rtrim($baseUrl, '/') . $this->urlGenerator->generate('telegram_bot_webhook', [
            'id' => $bot->getId(),
        ]);

        if ($this->botService->setWebhook($bot, $webhookUrl)) {
            $io->success(sprintf('Successfully set webhook URL to: %s', $webhookUrl));

            return Command::SUCCESS;
        }

        $io->error('Failed to set webhook URL');

        return Command::FAILURE;
    }
}
