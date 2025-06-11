<?php

namespace TelegramBotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;

class TelegramBotBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineAsyncInsertBundle::class => ['all' => true],
        ];
    }
}
