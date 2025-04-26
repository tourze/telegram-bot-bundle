<?php

namespace TelegramBotBundle\Controller\Admin;

use TelegramBotBundle\Entity\CommandLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class TelegramBotCommandLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CommandLog::class;
    }
}
