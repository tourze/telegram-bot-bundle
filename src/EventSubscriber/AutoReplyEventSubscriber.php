<?php

namespace TelegramBotBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Event\TelegramUpdateEvent;
use TelegramBotBundle\Repository\AutoReplyRuleRepository;
use TelegramBotBundle\Service\TelegramBotService;

/**
 * 处理 Telegram Bot 的自动回复
 */
readonly class AutoReplyEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AutoReplyRuleRepository $ruleRepository,
        private TelegramBotService $botService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TelegramUpdateEvent::class => ['onTelegramUpdate', 0],
        ];
    }

    public function onTelegramUpdate(TelegramUpdateEvent $event): void
    {
        $messageText = $event->getMessageText();
        if (null === $messageText || '' === trim($messageText)) {
            return;
        }

        $chatId = $event->getChatId();
        if (null === $chatId) {
            return;
        }

        // 查找匹配的自动回复规则
        $botId = $event->getBot()->getId();
        if (null === $botId) {
            return;
        }

        $rules = $this->ruleRepository->findMatchingRules(
            $botId,
            $messageText
        );

        foreach ($rules as $rule) {
            if ($this->isRuleMatched($rule, $messageText)) {
                $this->botService->sendMessage(
                    $event->getBot(),
                    (string) $chatId,
                    $rule->getReplyContent()
                );
                break;
            }
        }
    }

    private function isRuleMatched(AutoReplyRule $rule, string $messageText): bool
    {
        if ($rule->isExactMatch()) {
            return $messageText === $rule->getKeyword();
        }

        return str_contains($messageText, $rule->getKeyword());
    }
}
