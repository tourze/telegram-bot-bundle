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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TelegramBotBundle\Entity\CommandLog;

/**
 * @extends AbstractCrudController<CommandLog>
 */
#[AdminCrud(routePath: '/telegram-bot/command-log', routeName: 'telegram_bot_command_log')]
final class CommandLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CommandLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('命令日志')
            ->setEntityLabelInPlural('命令日志')
            ->setPageTitle('index', '命令日志列表')
            ->setPageTitle('detail', '命令日志详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'command', 'username', 'chatType'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
        ;
        yield AssociationField::new('bot', 'TG机器人');
        yield TextField::new('command', '命令名称');
        //        yield CodeEditorField::new('args', '命令参数')
        //            ->setLanguage('json')
        //            ->hideOnIndex();
        yield BooleanField::new('isSystem', '系统命令');
        yield NumberField::new('userId', '用户ID');
        yield TextField::new('username', '用户名');
        yield NumberField::new('chatId', '聊天ID');
        yield TextField::new('chatType', '聊天类型');
        yield DateTimeField::new('createTime', '创建时间');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Crud::PAGE_NEW, Crud::PAGE_EDIT)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('bot')
            ->add('command')
            ->add('isSystem')
            ->add('username')
            ->add('chatType')
            ->add('createTime')
        ;
    }
}
