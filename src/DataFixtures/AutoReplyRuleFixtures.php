<?php

namespace TelegramBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * Telegram自动回复规则数据填充
 */
#[When(env: 'test')]
class AutoReplyRuleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 获取机器人引用
        $demoBot = $this->getReference(TelegramBotFixtures::DEMO_BOT_REFERENCE, TelegramBot::class);

        // 创建欢迎词自动回复规则
        $welcomeRule = new AutoReplyRule();
        $welcomeRule->setBot($demoBot);
        $welcomeRule->setName('欢迎词');
        $welcomeRule->setKeyword('你好');
        $welcomeRule->setReplyContent('你好！我是演示机器人，很高兴为您服务！');
        $welcomeRule->setExactMatch(false);
        $welcomeRule->setPriority(100);
        $welcomeRule->setValid(true);

        $manager->persist($welcomeRule);

        // 创建感谢词自动回复规则
        $thanksRule = new AutoReplyRule();
        $thanksRule->setBot($demoBot);
        $thanksRule->setName('感谢词');
        $thanksRule->setKeyword('谢谢');
        $thanksRule->setReplyContent('不客气！如有其他问题，请随时告诉我。');
        $thanksRule->setExactMatch(false);
        $thanksRule->setPriority(90);
        $thanksRule->setValid(true);

        $manager->persist($thanksRule);

        // 创建精确匹配规则
        $exactRule = new AutoReplyRule();
        $exactRule->setBot($demoBot);
        $exactRule->setName('精确匹配');
        $exactRule->setKeyword('关于');
        $exactRule->setReplyContent('这是一个演示Telegram机器人，用于展示TelegramBotBundle的功能。');
        $exactRule->setExactMatch(true);
        $exactRule->setPriority(80);
        $exactRule->setValid(true);

        $manager->persist($exactRule);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TelegramBotFixtures::class,
        ];
    }
}
