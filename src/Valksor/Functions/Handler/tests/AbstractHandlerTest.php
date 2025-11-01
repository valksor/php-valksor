<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Davis Zalitis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Handler\Tests;

use Closure;
use PHPUnit\Framework\TestCase;
use Valksor\Functions\Handler\AbstractHandler;

use function is_array;
use function is_int;
use function is_string;

final class AbstractHandlerTest extends TestCase
{
    // Chain of Responsibility pattern tests

    public function testChainProcessesRequestThroughMultipleHandlers(): void
    {
        $handler1 = new ConditionalHandler(fn () => null);
        $handler2 = new ConditionalHandler(fn () => 'result2');
        $handler3 = new ConditionalHandler(fn () => 'result3');

        $handler1->next($handler2);
        $handler2->next($handler3);

        $this->assertSame('result2', $handler1->handle());
    }

    public function testChainProcessesWithDifferentArgumentTypes(): void
    {
        $handler1 = new PassThroughHandler();
        $handler2 = new ArgumentCaptureHandler();

        $handler1->next($handler2);
        $handler1->handle(42, 'string', [1, 2, 3], true);

        $this->assertSame([42, 'string', [1, 2, 3], true], $handler2->getCapturedArguments());
    }

    public function testChainReturnsNullWhenNoHandlerCanProcess(): void
    {
        $handler1 = new ConditionalHandler(fn () => null);
        $handler2 = new ConditionalHandler(fn () => null);
        $handler3 = new ConditionalHandler(fn () => null);

        $handler1->next($handler2);
        $handler2->next($handler3);

        $this->assertNull($handler1->handle());
    }

    public function testChainStopsAtFirstHandlerThatReturnsValue(): void
    {
        $handler1 = new ConditionalHandler(fn () => 'result1');
        $handler2 = new ConditionalHandler(fn () => 'result2');

        $handler1->next($handler2);

        $this->assertSame('result1', $handler1->handle());
    }

    // handle() method tests

    public function testHandleDelegatesToNextHandler(): void
    {
        $handler1 = new PassThroughHandler();
        $handler2 = new ConcreteHandler('result');

        $handler1->next($handler2);

        $this->assertSame('result', $handler1->handle());
    }

    public function testHandlePassesArgumentsToNextHandler(): void
    {
        $handler1 = new PassThroughHandler();
        $handler2 = new ArgumentCaptureHandler();

        $handler1->next($handler2);
        $handler1->handle('arg1', 'arg2', 'arg3');

        $this->assertSame(['arg1', 'arg2', 'arg3'], $handler2->getCapturedArguments());
    }

    public function testHandleReturnsNullWhenNoNextHandler(): void
    {
        $handler = new PassThroughHandler();

        $this->assertNull($handler->handle());
    }

    public function testHandlerCanDecideToProcessOrPass(): void
    {
        $handler1 = new ConditionalHandler(fn (...$args) => 'process' === $args[0] ? 'handled-by-1' : null);
        $handler2 = new ConditionalHandler(fn (...$args) => 'pass' === $args[0] ? 'handled-by-2' : null);

        $handler1->next($handler2);

        $this->assertSame('handled-by-1', $handler1->handle('process'));
        $this->assertSame('handled-by-2', $handler1->handle('pass'));
    }

    public function testHandlerReplacementInChain(): void
    {
        $handler1 = new PassThroughHandler();
        $handler2 = new ConcreteHandler('handler2');
        $handler3 = new ConcreteHandler('handler3');

        $handler1->next($handler2);

        // Replace handler2 with handler3
        $handler1->next($handler3);

        $this->assertSame('handler3', $handler1->handle());
    }

    public function testHandlerWithNoArguments(): void
    {
        $handler1 = new PassThroughHandler();
        $handler2 = new ConcreteHandler('no-args-result');

        $handler1->next($handler2);

        $this->assertSame('no-args-result', $handler1->handle());
    }

    public function testLongChainOfHandlers(): void
    {
        $handler1 = new ConditionalHandler(fn () => null);
        $handler2 = new ConditionalHandler(fn () => null);
        $handler3 = new ConditionalHandler(fn () => null);
        $handler4 = new ConditionalHandler(fn () => null);
        $handler5 = new ConditionalHandler(fn () => 'final-result');

        $handler1->next($handler2);
        $handler2->next($handler3);
        $handler3->next($handler4);
        $handler4->next($handler5);

        $this->assertSame('final-result', $handler1->handle());
    }

    public function testMultipleHandlersWithComplexLogic(): void
    {
        $numberHandler = new ConditionalHandler(fn (...$args) => is_int($args[0] ?? null) ? 'number' : null);
        $stringHandler = new ConditionalHandler(fn (...$args) => is_string($args[0] ?? null) ? 'string' : null);
        $arrayHandler = new ConditionalHandler(fn (...$args) => is_array($args[0] ?? null) ? 'array' : null);

        $numberHandler->next($stringHandler);
        $stringHandler->next($arrayHandler);

        $this->assertSame('number', $numberHandler->handle(42));
        $this->assertSame('string', $numberHandler->handle('test'));
        $this->assertSame('array', $numberHandler->handle([1, 2, 3]));
    }

    public function testNextCanBeChained(): void
    {
        $handler1 = new ConcreteHandler('handler1');
        $handler2 = new ConcreteHandler('handler2');
        $handler3 = new ConcreteHandler('handler3');

        $result = $handler1->next($handler2)->next($handler3);

        $this->assertSame($handler1, $result);
    }

    public function testNextReturnsCurrentHandler(): void
    {
        $handler1 = new ConcreteHandler('handler1');
        $handler2 = new ConcreteHandler('handler2');

        $result = $handler1->next($handler2);

        $this->assertSame($handler1, $result);
    }
    // next() method tests

    public function testNextSetsNextHandler(): void
    {
        $handler1 = new ConcreteHandler('handler1');
        $handler2 = new ConcreteHandler('handler2');

        $result = $handler1->next($handler2);

        $this->assertSame($handler1, $result);
    }
}

// Test helper classes

/**
 * Simple handler that returns a fixed value.
 */
class ConcreteHandler extends AbstractHandler
{
    public function __construct(
        private readonly mixed $returnValue,
    ) {
    }

    public function handle(
        ...$arguments,
    ): mixed {
        return $this->returnValue;
    }
}

/**
 * Handler that always passes to the next handler.
 */
class PassThroughHandler extends AbstractHandler
{
}

/**
 * Handler that captures arguments for testing.
 */
class ArgumentCaptureHandler extends AbstractHandler
{
    private array $capturedArguments = [];

    public function getCapturedArguments(): array
    {
        return $this->capturedArguments;
    }

    public function handle(
        ...$arguments,
    ): mixed {
        $this->capturedArguments = $arguments;

        return parent::handle(...$arguments);
    }
}

/**
 * Handler that uses a callback to decide whether to handle or pass.
 */
class ConditionalHandler extends AbstractHandler
{
    public function __construct(
        private readonly Closure $condition,
    ) {
    }

    public function handle(
        ...$arguments,
    ): mixed {
        $result = ($this->condition)(...$arguments);

        return $result ?? parent::handle(...$arguments);
    }
}
