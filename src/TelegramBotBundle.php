<?php

namespace TelegramBotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class TelegramBotBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineAsyncBundle\DoctrineAsyncBundle::class => ['all' => true],
        ];
    }
}
