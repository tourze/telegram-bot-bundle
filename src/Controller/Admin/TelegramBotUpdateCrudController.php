<?php

namespace TelegramBotBundle\Controller\Admin;

use TelegramBotBundle\Entity\TelegramUpdate;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class TelegramBotUpdateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TelegramUpdate::class;
    }
}
