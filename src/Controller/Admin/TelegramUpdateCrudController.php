<?php

namespace TelegramBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TelegramBotBundle\Entity\TelegramUpdate;

/**
 * @extends AbstractCrudController<TelegramUpdate>
 */
#[AdminCrud(routePath: '/telegram-bot/telegram-update', routeName: 'telegram_bot_telegram_update')]
final class TelegramUpdateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TelegramUpdate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息记录')
            ->setEntityLabelInPlural('消息记录')
            ->setPageTitle('index', '消息记录列表')
            ->setPageTitle('detail', '消息记录详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'updateId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
        ;
        yield AssociationField::new('bot', 'TG机器人');
        yield TextField::new('updateId', '更新ID');

        //        if (Crud::PAGE_DETAIL === $pageName) {
        //            yield CodeEditorField::new('rawData', '原始数据')
        //                ->setLanguage('json');
        //        }

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
            ->add('createTime')
        ;
    }
}
