<?php

namespace TelegramBotBundle\Controller\Admin;

use TelegramBotBundle\Entity\TelegramBot;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class TelegramBotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TelegramBot::class;
    }
}
