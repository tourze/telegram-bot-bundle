<?php

namespace TelegramBotBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TelegramBotBundle\Exception\InvalidCommandHandlerException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidCommandHandlerException::class)]
final class InvalidCommandHandlerExceptionTest extends AbstractExceptionTestCase
{
    public function testConstructor(): void
    {
        $message = 'Handler SomeHandler must implement CommandHandlerInterface';
        $exception = new InvalidCommandHandlerException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertInstanceOf(\LogicException::class, $exception);
    }

    public function testConstructorWithCode(): void
    {
        $message = 'Handler SomeHandler must implement CommandHandlerInterface';
        $code = 500;
        $exception = new InvalidCommandHandlerException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testConstructorWithPrevious(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $message = 'Handler SomeHandler must implement CommandHandlerInterface';
        $exception = new InvalidCommandHandlerException($message, 0, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
