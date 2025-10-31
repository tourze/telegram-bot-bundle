<?php

namespace TelegramBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TelegramBotBundle\Entity\TelegramBot;

/**
 * @extends AbstractCrudController<TelegramBot>
 */
#[AdminCrud(routePath: '/telegram-bot/telegram-bot', routeName: 'telegram_bot_telegram_bot')]
final class TelegramBotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TelegramBot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('TG机器人')
            ->setEntityLabelInPlural('TG机器人')
            ->setPageTitle('index', 'TG机器人列表')
            ->setPageTitle('new', '新增TG机器人')
            ->setPageTitle('edit', '编辑TG机器人')
            ->setPageTitle('detail', 'TG机器人详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'username', 'token'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;
        yield TextField::new('name', '机器人名称');
        yield TextField::new('username', '机器人用户名');
        yield TextField::new('token', '机器人Token');
        yield TextField::new('webhookUrl', 'Webhook URL');
        yield TextareaField::new('description', '描述')
            ->hideOnIndex()
        ;
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
            ->add('name')
            ->add('username')
            ->add('valid')
            ->add('createTime')
        ;
    }
}
