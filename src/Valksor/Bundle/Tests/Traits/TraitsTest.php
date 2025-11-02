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

namespace Valksor\Bundle\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Valksor\Bundle\Traits\_GetClassFromFile;
use Valksor\Bundle\Traits\_LoadReflection;
use Valksor\Functions\Memoize\MemoizeCache;

use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class TraitsTest extends TestCase
{
    public function testGetClassFromFileParsesClassName(): void
    {
        $helper = new class {
            use _GetClassFromFile;
        };
        $memoize = new MemoizeCache();

        $file = tempnam(sys_get_temp_dir(), 'valksor_class_');
        file_put_contents($file, "<?php\nnamespace Demo\\Example;\nclass SampleClass {}\n");

        try {
            $class = $helper->getClassFromFile($file, $memoize);
            self::assertSame('Demo\\Example\\SampleClass', $class);

            // Ensure memoized result is reused when called again
            self::assertSame($class, $helper->getClassFromFile($file, $memoize));
            self::assertNull($helper->getClassFromFile(null, $memoize));
        } finally {
            unlink($file);
        }
    }

    public function testLoadReflectionCachesInstances(): void
    {
        $helper = new class {
            use _LoadReflection;
        };
        $memoize = new MemoizeCache();

        $first = $helper->loadReflection(self::class, $memoize);
        $second = $helper->loadReflection(self::class, $memoize);

        self::assertSame($first, $second);

        $objectReflection = $helper->loadReflection($this, $memoize);
        self::assertSame($first, $objectReflection);
    }
}
