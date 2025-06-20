<?php

namespace TelegramBotBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use TelegramBotBundle\Entity\AutoReplyRule;
use TelegramBotBundle\Entity\TelegramBot;

class AutoReplyRuleTest extends TestCase
{
    private AutoReplyRule $autoReplyRule;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        $this->autoReplyRule = new AutoReplyRule();
        $this->bot = new TelegramBot();
    }

    public function test_setAndGetBot_withValidBot(): void
    {
        $this->autoReplyRule->setBot($this->bot);
        $this->assertSame($this->bot, $this->autoReplyRule->getBot());
    }

    public function test_setAndGetName_withValidName(): void
    {
        $name = 'Test Rule';
        $this->autoReplyRule->setName($name);
        $this->assertEquals($name, $this->autoReplyRule->getName());
    }

    public function test_setAndGetName_withEmptyString(): void
    {
        $this->autoReplyRule->setName('');
        $this->assertEquals('', $this->autoReplyRule->getName());
    }

    public function test_setAndGetKeyword_withValidKeyword(): void
    {
        $keyword = 'hello';
        $this->autoReplyRule->setKeyword($keyword);
        $this->assertEquals($keyword, $this->autoReplyRule->getKeyword());
    }

    public function test_setAndGetKeyword_withSpecialCharacters(): void
    {
        $keyword = '你好@#$%^&*()';
        $this->autoReplyRule->setKeyword($keyword);
        $this->assertEquals($keyword, $this->autoReplyRule->getKeyword());
    }

    public function test_setAndGetReplyContent_withValidContent(): void
    {
        $content = 'Hello! How can I help you?';
        $this->autoReplyRule->setReplyContent($content);
        $this->assertEquals($content, $this->autoReplyRule->getReplyContent());
    }

    public function test_setAndGetReplyContent_withHtmlContent(): void
    {
        $content = '<b>Bold</b> and <i>italic</i> text';
        $this->autoReplyRule->setReplyContent($content);
        $this->assertEquals($content, $this->autoReplyRule->getReplyContent());
    }

    public function test_setAndGetReplyContent_withEmptyContent(): void
    {
        $this->autoReplyRule->setReplyContent('');
        $this->assertEquals('', $this->autoReplyRule->getReplyContent());
    }

    public function test_setAndGetExactMatch_withTrue(): void
    {
        $this->autoReplyRule->setExactMatch(true);
        $this->assertTrue($this->autoReplyRule->isExactMatch());
    }

    public function test_setAndGetExactMatch_withFalse(): void
    {
        $this->autoReplyRule->setExactMatch(false);
        $this->assertFalse($this->autoReplyRule->isExactMatch());
    }

    public function test_setAndGetPriority_withPositiveNumber(): void
    {
        $priority = 100;
        $this->autoReplyRule->setPriority($priority);
        $this->assertEquals($priority, $this->autoReplyRule->getPriority());
    }

    public function test_setAndGetPriority_withZero(): void
    {
        $this->autoReplyRule->setPriority(0);
        $this->assertEquals(0, $this->autoReplyRule->getPriority());
    }

    public function test_setAndGetPriority_withNegativeNumber(): void
    {
        $priority = -10;
        $this->autoReplyRule->setPriority($priority);
        $this->assertEquals($priority, $this->autoReplyRule->getPriority());
    }

    public function test_setAndGetValid_withTrue(): void
    {
        $this->autoReplyRule->setValid(true);
        $this->assertTrue($this->autoReplyRule->isValid());
    }

    public function test_setAndGetValid_withFalse(): void
    {
        $this->autoReplyRule->setValid(false);
        $this->assertFalse($this->autoReplyRule->isValid());
    }

    public function test_setAndGetValid_withNull(): void
    {
        $this->autoReplyRule->setValid(null);
        $this->assertNull($this->autoReplyRule->isValid());
    }

    public function test_setAndGetCreatedBy_withValidString(): void
    {
        $createdBy = 'admin_user';
        $this->autoReplyRule->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->autoReplyRule->getCreatedBy());
    }

    public function test_setAndGetCreatedBy_withNull(): void
    {
        $this->autoReplyRule->setCreatedBy(null);
        $this->assertNull($this->autoReplyRule->getCreatedBy());
    }

    public function test_setAndGetUpdatedBy_withValidString(): void
    {
        $updatedBy = 'editor_user';
        $this->autoReplyRule->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->autoReplyRule->getUpdatedBy());
    }

    public function test_setAndGetUpdatedBy_withNull(): void
    {
        $this->autoReplyRule->setUpdatedBy(null);
        $this->assertNull($this->autoReplyRule->getUpdatedBy());
    }

    public function test_setAndGetCreateTime_withValidDateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $this->autoReplyRule->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->autoReplyRule->getCreateTime());
    }

    public function test_setAndGetCreateTime_withNull(): void
    {
        $this->autoReplyRule->setCreateTime(null);
        $this->assertNull($this->autoReplyRule->getCreateTime());
    }

    public function test_setAndGetUpdateTime_withValidDateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2023-01-01 11:00:00');
        $this->autoReplyRule->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->autoReplyRule->getUpdateTime());
    }

    public function test_setAndGetUpdateTime_withNull(): void
    {
        $this->autoReplyRule->setUpdateTime(null);
        $this->assertNull($this->autoReplyRule->getUpdateTime());
    }

    public function test_defaultValues(): void
    {
        $rule = new AutoReplyRule();
        $this->assertEquals(0, $rule->getId());
        $this->assertEquals('', $rule->getName());
        $this->assertEquals('', $rule->getKeyword());
        $this->assertEquals('', $rule->getReplyContent());
        $this->assertFalse($rule->isExactMatch());
        $this->assertEquals(0, $rule->getPriority());
        $this->assertFalse($rule->isValid());
        $this->assertNull($rule->getCreatedBy());
        $this->assertNull($rule->getUpdatedBy());
        $this->assertNull($rule->getCreateTime());
        $this->assertNull($rule->getUpdateTime());
    }

    public function test_fluentInterface(): void
    {
        $result = $this->autoReplyRule
            ->setBot($this->bot)
            ->setName('Test')
            ->setKeyword('hello')
            ->setReplyContent('Hi there!')
            ->setExactMatch(true)
            ->setPriority(100)
            ->setValid(true);

        $this->assertSame($this->autoReplyRule, $result);
        $this->assertEquals('Test', $this->autoReplyRule->getName());
        $this->assertEquals('hello', $this->autoReplyRule->getKeyword());
        $this->assertEquals('Hi there!', $this->autoReplyRule->getReplyContent());
        $this->assertTrue($this->autoReplyRule->isExactMatch());
        $this->assertEquals(100, $this->autoReplyRule->getPriority());
        $this->assertTrue($this->autoReplyRule->isValid());
    }
}
