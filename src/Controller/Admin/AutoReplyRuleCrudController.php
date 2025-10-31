<?php

namespace TelegramBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TelegramBotBundle\Entity\AutoReplyRule;

/**
 * @extends AbstractCrudController<AutoReplyRule>
 */
#[AdminCrud(routePath: '/telegram-bot/auto-reply-rule', routeName: 'telegram_bot_auto_reply_rule')]
final class AutoReplyRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutoReplyRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('自动回复规则')
            ->setEntityLabelInPlural('自动回复规则')
            ->setPageTitle('index', '自动回复规则列表')
            ->setPageTitle('new', '新增自动回复规则')
            ->setPageTitle('edit', '编辑自动回复规则')
            ->setPageTitle('detail', '自动回复规则详情')
            ->setDefaultSort(['priority' => 'DESC'])
            ->setSearchFields(['id', 'name', 'keyword', 'replyContent'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;
        yield AssociationField::new('bot', 'TG机器人');
        yield TextField::new('name', '规则名称');
        yield TextField::new('keyword', '匹配关键词');
        yield TextareaField::new('replyContent', '回复内容')
            ->hideOnIndex()
        ;
        yield BooleanField::new('exactMatch', '精确匹配');
        yield IntegerField::new('priority', '优先级');
        yield BooleanField::new('valid', '有效');
        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
        ;
        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
        ;
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('bot')
            ->add('name')
            ->add('keyword')
            ->add('exactMatch')
            ->add('valid')
            ->add('createTime')
        ;
    }
}
