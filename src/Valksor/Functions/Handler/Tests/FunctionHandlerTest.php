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

use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Handler\FunctionHandler;

use function strtoupper;

final class FunctionHandlerTest extends TestCase
{
    public function testChainOfFunctionHandlers(): void
    {
        $instance1 = new TestClass();
        $instance2 = new TestClass();

        $handler1 = new FunctionHandler('returnNull', $instance1);
        $handler2 = new FunctionHandler('returnNull', $instance2);
        $handler3 = new FunctionHandler('strtoupper');

        $handler1->next($handler2);
        $handler2->next($handler3);

        $result = $handler1->handle('final');
        $this->assertSame('FINAL', $result);
    }

    public function testChainPriorityWithNonNullResults(): void
    {
        $handler1 = new FunctionHandler('strtoupper');
        $handler2 = new FunctionHandler('strtolower');

        $handler1->next($handler2);

        // Next handler result takes precedence due to ?? operator
        $result = $handler1->handle('hello');
        $this->assertSame('hello', $result);
    }
    // Constructor tests

    public function testFunctionHandlerPreservesArgumentTypes(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('echo', $instance)->handle(42, 'string', [1, 2], true);
        $this->assertSame([42, 'string', [1, 2], true], $result);
    }

    public function testFunctionHandlerWithDifferentObjectInstances(): void
    {
        $instance1 = new TestClass();
        $instance2 = new AnotherTestClass();

        $handler1 = new FunctionHandler('getValue', $instance1);
        $handler2 = new FunctionHandler('getName', $instance2);

        $this->assertSame('test-value', $handler1->handle());
        $this->assertSame('test-name', $handler2->handle());
    }

    public function testFunctionHandlerWithObjectReturningObject(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('getObject', $instance)->handle();

        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertSame('object-value', $result->value);
    }

    public function testFunctionHandlerWithVariadicArguments(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('sum', $instance)->handle(1, 2, 3, 4, 5);
        $this->assertSame(15, $result);
    }

    // Global function execution tests

    public function testHandleExecutesGlobalFunction(): void
    {
        $result = new FunctionHandler('strtoupper')->handle('hello');
        $this->assertSame('HELLO', $result);
    }

    public function testHandleExecutesGlobalFunctionWithMultipleArguments(): void
    {
        $result = new FunctionHandler('str_replace')->handle('world', 'PHP', 'hello world');
        $this->assertSame('hello PHP', $result);
    }

    public function testHandleExecutesGlobalFunctionWithNoArguments(): void
    {
        $result = new FunctionHandler('time')->handle();
        $this->assertIsInt($result);
    }

    // Object method execution tests

    public function testHandleExecutesObjectMethod(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('getValue', $instance)->handle();
        $this->assertSame('test-value', $result);
    }

    public function testHandleExecutesObjectMethodReturningArray(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('getArray', $instance)->handle();
        $this->assertSame([1, 2, 3], $result);
    }

    public function testHandleExecutesObjectMethodWithArguments(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('add', $instance)->handle(5, 3);
        $this->assertSame(8, $result);
    }

    public function testHandleExecutesObjectMethodWithSingleArgument(): void
    {
        $instance = new TestClass();
        $result = new FunctionHandler('uppercase', $instance)->handle('hello');
        $this->assertSame('HELLO', $result);
    }

    public function testHandleExecutesStrlen(): void
    {
        $result = new FunctionHandler('strlen')->handle('test');
        $this->assertSame(4, $result);
    }

    public function testHandleExecutesStrtolower(): void
    {
        $result = new FunctionHandler('strtolower')->handle('HELLO WORLD');
        $this->assertSame('hello world', $result);
    }

    // Chain integration tests

    public function testHandleWithNextHandlerWhenResultIsNotNull(): void
    {
        $handler1 = new FunctionHandler('strtoupper');
        $handler2 = new FunctionHandler('strtolower');

        $handler1->next($handler2);

        // Next handler takes precedence due to ?? operator in FunctionHandler
        $result = $handler1->handle('hello');
        $this->assertSame('hello', $result);
    }

    public function testHandleWithNextHandlerWhenResultIsNull(): void
    {
        $instance = new TestClass();
        $handler1 = new FunctionHandler('returnNull', $instance);
        $handler2 = new FunctionHandler('strtoupper');

        $handler1->next($handler2);

        $result = $handler1->handle('fallback');
        $this->assertSame('FALLBACK', $result);
    }

    // Complex scenarios

    public function testMultipleFunctionHandlersWithDifferentFunctions(): void
    {
        $result1 = new FunctionHandler('strtoupper')->handle('hello');
        $result2 = new FunctionHandler('strlen')->handle('test');

        $this->assertSame('HELLO', $result1);
        $this->assertSame(4, $result2);
    }

    // Static helper class tests

    public function testStaticHelperIsCreatedOnce(): void
    {
        $handler = new FunctionHandler('strtoupper');
        $result1 = $handler->handle('first');
        $result2 = $handler->handle('second');

        $this->assertSame('FIRST', $result1);
        $this->assertSame('SECOND', $result2);
    }
}

// Test helper classes

class TestClass
{
    public function add(
        int $a,
        int $b,
    ): int {
        return $a + $b;
    }

    public function echo(
        ...$args,
    ): array {
        return $args;
    }

    public function getArray(): array
    {
        return [1, 2, 3];
    }

    public function getObject(): stdClass
    {
        $obj = new stdClass();
        $obj->value = 'object-value';

        return $obj;
    }

    public function getValue(): string
    {
        return 'test-value';
    }

    public function returnNull(): null
    {
        return null;
    }

    public function sum(
        ...$numbers,
    ): int {
        return array_sum($numbers);
    }

    public function uppercase(
        string $value,
    ): string {
        return strtoupper($value);
    }
}

class AnotherTestClass
{
    public function getName(): string
    {
        return 'test-name';
    }
}
