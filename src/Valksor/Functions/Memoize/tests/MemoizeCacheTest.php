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
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Valksor\Functions\Memoize\Memoize;
use Valksor\Functions\Memoize\MemoizeCache;
use Valksor\Functions\Memoize\Tests\Fixtures\TestContext;

final class MemoizeCacheTest extends TestCase
{
    private MemoizeCache $memoizeCache;

    // =========================================================================
    // Tests for MemoizeCache class
    // =========================================================================

    public function testMemoizeCacheClassExists(): void
    {
        // This test ensures MemoizeCache class is loaded and the conditional
        // definition in MemoizeCache.php (line 19) is evaluated
        $this->assertTrue(class_exists(MemoizeCache::class));
        $this->assertInstanceOf(Memoize::class, new MemoizeCache());
    }

    public function testMemoizeCacheHasAutoconfigureAttribute(): void
    {
        if (!class_exists(Autoconfigure::class)) {
            $this->markTestSkipped('Symfony Autoconfigure attribute not available');
        }

        $attributes = new ReflectionClass(MemoizeCache::class)->getAttributes(Autoconfigure::class);

        $this->assertNotEmpty($attributes, 'MemoizeCache should have Autoconfigure attribute when Symfony is available');

        if (!empty($attributes)) {
            $attribute = $attributes[0]->newInstance();
            $this->assertTrue($attribute->public, 'Autoconfigure should have public=true');
            $this->assertTrue($attribute->shared, 'Autoconfigure should have shared=true');
        }
    }

    public function testMemoizeCacheHasSameFunctionalityAsMemoize(): void
    {
        $result = $this->memoizeCache->memoize(
            TestContext::USER,
            'test-key',
            fn () => 'test-value',
        );

        $this->assertSame('test-value', $result);
    }

    public function testMemoizeCacheRefreshFunctionality(): void
    {
        $counter = 0;
        $callback = function () use (&$counter) {
            $counter++;

            return "value-$counter";
        };

        // First call
        $result1 = $this->memoizeCache->memoize(TestContext::USER, 'key1', $callback);
        $this->assertSame('value-1', $result1);

        // Second call with refresh should re-execute
        $result2 = $this->memoizeCache->memoize(TestContext::USER, 'key1', $callback, true);
        $this->assertSame('value-2', $result2);
    }

    public function testMemoizeCacheValueMethodWorks(): void
    {
        // First, cache a value
        $this->memoizeCache->memoize(TestContext::CACHE, 'key1', fn () => 'cached-value');

        // Then retrieve it
        $result = $this->memoizeCache->value(TestContext::CACHE, 'key1');

        $this->assertSame('cached-value', $result);
    }

    public function testMemoizeCacheWithSubKeys(): void
    {
        $result = $this->memoizeCache->memoize(
            TestContext::PRODUCT,
            'product',
            fn () => 'nested-cache-value',
            false,
            'details',
            'price',
        );

        $this->assertSame('nested-cache-value', $result);

        // Retrieve with value()
        $retrieved = $this->memoizeCache->value(TestContext::PRODUCT, 'product', null, 'details', 'price');
        $this->assertSame('nested-cache-value', $retrieved);
    }

    protected function setUp(): void
    {
        $this->memoizeCache = new MemoizeCache();
    }
}
