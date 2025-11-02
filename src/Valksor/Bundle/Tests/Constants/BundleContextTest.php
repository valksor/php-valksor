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

namespace Valksor\Bundle\Tests\Constants;

use PHPUnit\Framework\TestCase;
use Valksor\Bundle\Constants\BundleContext;

final class BundleContextTest extends TestCase
{
    public function testEnumValuesAreStable(): void
    {
        self::assertSame('VALKSOR_BUNDLE_CALL_CLASS', BundleContext::CALLER_CLASS->value);
        self::assertSame('VALKSOR_BUNDLE_PLURAL', BundleContext::PLURAL->value);
        self::assertSame('VALKSOR_BUNDLE_READ_PROPERTY', BundleContext::READ_PROPERTY->value);
        self::assertSame('VALKSOR_BUNDLE_REFLECTION', BundleContext::REFLECTION->value);
    }
}
