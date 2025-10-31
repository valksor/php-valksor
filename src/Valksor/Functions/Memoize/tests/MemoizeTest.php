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

namespace Valksor\Functions\Memoize\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Memoize\Memoize;
use Valksor\Functions\Memoize\Tests\Fixtures\TestContext;

final class MemoizeTest extends TestCase
{
    private int $callbackExecutionCount;
    private Memoize $memoize;

    public function testMemoizeCreatesIntermediateLevels(): void
    {
        // Create a deep nested value
        $this->memoize->memoize(
            TestContext::USER,
            'root',
            fn () => 'deep-value',
            false,
            'branch1',
            'branch2',
            'leaf',
        );

        // Now access intermediate level
        $intermediate = $this->memoize->value(TestContext::USER, 'root', null, 'branch1', 'branch2');

        $this->assertIsArray($intermediate);
        $this->assertArrayHasKey('leaf', $intermediate);
        $this->assertSame('deep-value', $intermediate['leaf']);
    }

    // =========================================================================
    // Additional edge case tests for 100% coverage
    // =========================================================================

    public function testMemoizeCreatesNestedStructureFromScratch(): void
    {
        // Test that nested structure is created properly when starting from empty cache
        $result = $this->memoize->memoize(
            TestContext::PRODUCT,
            'level1',
            fn () => 'nested-from-scratch',
            false,
            'level2',
            'level3',
        );

        $this->assertSame('nested-from-scratch', $result);

        // Verify we can retrieve it
        $retrieved = $this->memoize->value(TestContext::PRODUCT, 'level1', null, 'level2', 'level3');
        $this->assertSame('nested-from-scratch', $retrieved);
    }

    // =========================================================================
    // Tests for memoize() method - Basic functionality
    // =========================================================================

    public function testMemoizeExecutesCallbackAndReturnsValue(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'test-key',
            fn () => 'test-value',
        );

        $this->assertSame('test-value', $result);
    }

    public function testMemoizeInitializesContextWhenNull(): void
    {
        // Test that a new context is properly initialized
        $result = $this->memoize->memoize(
            TestContext::ORDER,
            'new-key',
            fn () => 'context-init-value',
        );

        $this->assertSame('context-init-value', $result);
    }

    public function testMemoizeOverwritesValueWhenRefreshIsTrue(): void
    {
        $counter = 0;
        $callback = function () use (&$counter) {
            $counter++;

            return "value-$counter";
        };

        // First call
        $result1 = $this->memoize->memoize(TestContext::USER, 'refresh-test', $callback);
        $this->assertSame('value-1', $result1);

        // Second call with refresh=true should overwrite
        $result2 = $this->memoize->memoize(TestContext::USER, 'refresh-test', $callback, true);
        $this->assertSame('value-2', $result2);

        // Third call without refresh should return cached (overwritten) value
        $result3 = $this->memoize->memoize(TestContext::USER, 'refresh-test', $callback);
        $this->assertSame('value-2', $result3);
    }

    public function testMemoizeReturnsCachedValueOnSubsequentCalls(): void
    {
        $callback = function () {
            $this->callbackExecutionCount++;

            return 'cached-value';
        };

        // First call - callback should execute
        $result1 = $this->memoize->memoize(TestContext::USER, 'key1', $callback);
        $this->assertSame('cached-value', $result1);
        $this->assertSame(1, $this->callbackExecutionCount);

        // Second call - should return cached value without executing callback
        $result2 = $this->memoize->memoize(TestContext::USER, 'key1', $callback);
        $this->assertSame('cached-value', $result2);
        $this->assertSame(1, $this->callbackExecutionCount, 'Callback should not be executed again');
    }

    public function testMemoizeSubKeysAreIsolated(): void
    {
        $callback1 = fn () => 'value1';
        $callback2 = fn () => 'value2';

        $result1 = $this->memoize->memoize(TestContext::USER, 'user', $callback1, false, 'profile');
        $result2 = $this->memoize->memoize(TestContext::USER, 'user', $callback2, false, 'settings');

        $this->assertSame('value1', $result1);
        $this->assertSame('value2', $result2);
    }

    public function testMemoizeWithArrayReturn(): void
    {
        $array = ['key' => 'value', 'number' => 123];
        $result = $this->memoize->memoize(
            TestContext::USER,
            'array-test',
            fn () => $array,
        );

        $this->assertIsArray($result);
        $this->assertSame($array, $result);
    }

    public function testMemoizeWithBooleanFalseReturn(): void
    {
        // Test caching boolean false (important for array_key_exists check)
        $result = $this->memoize->memoize(
            TestContext::USER,
            'false-test',
            fn () => false,
        );

        $this->assertFalse($result);

        // Verify it's cached and not re-executed
        $callback = function () {
            $this->callbackExecutionCount++;

            return true; // Different value
        };

        $result2 = $this->memoize->memoize(TestContext::USER, 'false-test', $callback);
        $this->assertFalse($result2, 'Should return cached false, not execute callback');
        $this->assertSame(0, $this->callbackExecutionCount, 'Callback should not execute');
    }

    // =========================================================================
    // Tests for memoize() method - Context isolation
    // =========================================================================

    public function testMemoizeWithDifferentContextsAreIsolated(): void
    {
        $callback1 = fn () => 'user-value';
        $callback2 = fn () => 'product-value';

        $result1 = $this->memoize->memoize(TestContext::USER, 'same-key', $callback1);
        $result2 = $this->memoize->memoize(TestContext::PRODUCT, 'same-key', $callback2);

        $this->assertSame('user-value', $result1);
        $this->assertSame('product-value', $result2);
    }

    public function testMemoizeWithDifferentKeysExecutesSeparateCallbacks(): void
    {
        $callback1 = fn () => 'value1';
        $callback2 = fn () => 'value2';

        $result1 = $this->memoize->memoize(TestContext::USER, 'key1', $callback1);
        $result2 = $this->memoize->memoize(TestContext::USER, 'key2', $callback2);

        $this->assertSame('value1', $result1);
        $this->assertSame('value2', $result2);
    }

    public function testMemoizeWithEmptyStringAsKey(): void
    {
        // Test with empty string as key (edge case)
        $result = $this->memoize->memoize(
            TestContext::USER,
            '',
            fn () => 'empty-string-key-value',
        );

        $this->assertSame('empty-string-key-value', $result);

        // Verify retrieval works
        $retrieved = $this->memoize->value(TestContext::USER, '');
        $this->assertSame('empty-string-key-value', $retrieved);
    }

    public function testMemoizeWithIntReturn(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'int-test',
            fn () => 42,
        );

        $this->assertIsInt($result);
        $this->assertSame(42, $result);
    }

    // =========================================================================
    // Tests for memoize() method - Key variations
    // =========================================================================

    public function testMemoizeWithIntegerKey(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            42,
            fn () => 'int-key-value',
        );

        $this->assertSame('int-key-value', $result);
    }

    public function testMemoizeWithMultipleSubKeys(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'user',
            fn () => 'deep-nested-value',
            false,
            'profile',
            'settings',
            'theme',
        );

        $this->assertSame('deep-nested-value', $result);
    }

    public function testMemoizeWithNullReturn(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'null-test',
            fn () => null,
        );

        $this->assertNull($result);
    }

    public function testMemoizeWithObjectReturn(): void
    {
        $object = new stdClass();
        $object->property = 'value';

        $result = $this->memoize->memoize(
            TestContext::USER,
            'object-test',
            fn () => $object,
        );

        $this->assertIsObject($result);
        $this->assertSame($object, $result);
    }

    // =========================================================================
    // Tests for memoize() method - Refresh behavior
    // =========================================================================

    public function testMemoizeWithRefreshForcesCallbackExecution(): void
    {
        $callback = function () {
            $this->callbackExecutionCount++;

            return 'value-' . $this->callbackExecutionCount;
        };

        // First call
        $result1 = $this->memoize->memoize(TestContext::USER, 'key1', $callback);
        $this->assertSame('value-1', $result1);
        $this->assertSame(1, $this->callbackExecutionCount);

        // Second call with refresh=false (default) - should use cache
        $result2 = $this->memoize->memoize(TestContext::USER, 'key1', $callback);
        $this->assertSame('value-1', $result2);
        $this->assertSame(1, $this->callbackExecutionCount);

        // Third call with refresh=true - should re-execute callback
        $result3 = $this->memoize->memoize(TestContext::USER, 'key1', $callback, true);
        $this->assertSame('value-2', $result3);
        $this->assertSame(2, $this->callbackExecutionCount);
    }

    // =========================================================================
    // Tests for memoize() method - Sub-key nesting
    // =========================================================================

    public function testMemoizeWithSingleSubKey(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'user',
            fn () => 'nested-value',
            false,
            'profile',
        );

        $this->assertSame('nested-value', $result);
    }

    public function testMemoizeWithStringKey(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'string-key',
            fn () => 'string-key-value',
        );

        $this->assertSame('string-key-value', $result);
    }

    // =========================================================================
    // Tests for memoize() method - Return type handling
    // =========================================================================

    public function testMemoizeWithStringReturn(): void
    {
        $result = $this->memoize->memoize(
            TestContext::USER,
            'string-test',
            fn () => 'string-value',
        );

        $this->assertIsString($result);
        $this->assertSame('string-value', $result);
    }

    public function testMemoizeWithZeroAsKey(): void
    {
        // Test with 0 as integer key (edge case)
        $result = $this->memoize->memoize(
            TestContext::USER,
            0,
            fn () => 'zero-key-value',
        );

        $this->assertSame('zero-key-value', $result);

        // Verify retrieval works
        $retrieved = $this->memoize->value(TestContext::USER, 0);
        $this->assertSame('zero-key-value', $retrieved);
    }

    public function testValueInitializesContextWhenRetrieving(): void
    {
        // value() should handle uninitialized context gracefully
        $result = $this->memoize->value(TestContext::CACHE, 'any-key', 'fallback');

        $this->assertSame('fallback', $result);
    }

    // =========================================================================
    // Tests for value() method - Basic retrieval
    // =========================================================================

    public function testValueRetrievesExistingCachedValue(): void
    {
        // First, cache a value
        $this->memoize->memoize(TestContext::USER, 'cached-key', fn () => 'cached-value');

        // Then retrieve it with value()
        $result = $this->memoize->value(TestContext::USER, 'cached-key');

        $this->assertSame('cached-value', $result);
    }

    public function testValueReturnsDefaultForNonExistentContext(): void
    {
        $result = $this->memoize->value(TestContext::ORDER, 'any-key', 'default-value');

        $this->assertSame('default-value', $result);
    }

    public function testValueReturnsDefaultForNonExistentKey(): void
    {
        $result = $this->memoize->value(TestContext::USER, 'non-existent-key', 'default-value');

        $this->assertSame('default-value', $result);
    }

    public function testValueReturnsDefaultForNonExistentSubKey(): void
    {
        // Cache value without sub-key
        $this->memoize->memoize(TestContext::USER, 'user', fn () => 'value');

        // Try to retrieve with non-existent sub-key
        $result = $this->memoize->value(TestContext::USER, 'user', 'default', 'non-existent');

        $this->assertSame('default', $result);
    }

    public function testValueReturnsNullDefaultWhenNotSpecified(): void
    {
        $result = $this->memoize->value(TestContext::USER, 'non-existent-key');

        $this->assertNull($result);
    }

    public function testValueWithArrayDefault(): void
    {
        $default = ['default' => 'array'];
        $result = $this->memoize->value(TestContext::USER, 'key', $default);

        $this->assertSame($default, $result);
    }

    public function testValueWithEmptySubKeysArray(): void
    {
        // Cache a simple value
        $this->memoize->memoize(TestContext::USER, 'simple', fn () => 'simple-value');

        // Retrieve without any subkeys (empty variadic)
        $result = $this->memoize->value(TestContext::USER, 'simple');

        $this->assertSame('simple-value', $result);
    }

    public function testValueWithIntDefault(): void
    {
        $result = $this->memoize->value(TestContext::USER, 'key', 0);

        $this->assertSame(0, $result);
    }

    public function testValueWithIntermediatePathThatDoesntExist(): void
    {
        // Cache nested structure with some values
        $this->memoize->memoize(TestContext::USER, 'root', fn () => 'value1', false, 'level1');

        // Try to access a path that doesn't exist at an intermediate level
        $result = $this->memoize->value(TestContext::USER, 'root', 'default', 'nonexistent', 'deep');

        $this->assertSame('default', $result);
    }

    public function testValueWithMultipleSubKeys(): void
    {
        // Cache value with multiple sub-keys
        $this->memoize->memoize(
            TestContext::USER,
            'user',
            fn () => 'deep-value',
            false,
            'profile',
            'settings',
            'theme',
        );

        // Retrieve with sub-keys
        $result = $this->memoize->value(TestContext::USER, 'user', null, 'profile', 'settings', 'theme');

        $this->assertSame('deep-value', $result);
    }

    // =========================================================================
    // Tests for value() method - Sub-key retrieval
    // =========================================================================

    public function testValueWithSingleSubKey(): void
    {
        // Cache value with sub-key
        $this->memoize->memoize(TestContext::USER, 'user', fn () => 'profile-value', false, 'profile');

        // Retrieve with sub-key
        $result = $this->memoize->value(TestContext::USER, 'user', null, 'profile');

        $this->assertSame('profile-value', $result);
    }

    // =========================================================================
    // Tests for value() method - Different default types
    // =========================================================================

    public function testValueWithStringDefault(): void
    {
        $result = $this->memoize->value(TestContext::USER, 'key', 'string-default');

        $this->assertSame('string-default', $result);
    }

    protected function setUp(): void
    {
        $this->memoize = new Memoize();
        $this->callbackExecutionCount = 0;
    }
}
