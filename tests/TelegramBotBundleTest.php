<?php

declare(strict_types=1);

namespace TelegramBotBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TelegramBotBundle\TelegramBotBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(TelegramBotBundle::class)]
#[RunTestsInSeparateProcesses]
final class TelegramBotBundleTest extends AbstractBundleTestCase
{
}
