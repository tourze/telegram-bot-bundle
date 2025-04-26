<?php

namespace TelegramBotBundle\Controller\Admin;

use TelegramBotBundle\Entity\BotCommand;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class TelegramBotCommandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BotCommand::class;
    }
}
