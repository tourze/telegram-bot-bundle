<?php

namespace TelegramBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TelegramBotBundle\Entity\BotCommand;

class BotCommandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BotCommand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('机器人命令')
            ->setEntityLabelInPlural('机器人命令')
            ->setPageTitle('index', '机器人命令列表')
            ->setPageTitle('new', '新增机器人命令')
            ->setPageTitle('edit', '编辑机器人命令')
            ->setPageTitle('detail', '机器人命令详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'command', 'handler', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setMaxLength(9999)
            ->hideOnForm();
        yield AssociationField::new('bot', 'TG机器人');
        yield TextField::new('command', '命令名称');
        yield TextField::new('handler', '命令处理器类');
        yield TextField::new('description', '命令描述');
        yield BooleanField::new('valid', '有效');
        yield TextField::new('createdBy', '创建人')
            ->hideOnForm();
        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm();
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('bot')
            ->add('command')
            ->add('valid')
            ->add('createTime');
    }
}
