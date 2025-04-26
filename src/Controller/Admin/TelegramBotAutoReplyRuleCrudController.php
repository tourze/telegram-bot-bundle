<?php

namespace TelegramBotBundle\Controller\Admin;

use TelegramBotBundle\Entity\AutoReplyRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class TelegramBotAutoReplyRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutoReplyRule::class;
    }
}
