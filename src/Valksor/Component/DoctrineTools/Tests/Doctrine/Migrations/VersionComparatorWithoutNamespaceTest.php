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

namespace Valksor\Component\DoctrineTools\Tests\Doctrine\Migrations;

use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\TestCase;
use Valksor\Component\DoctrineTools\Doctrine\Migrations\VersionComparatorWithoutNamespace;

/**
 * @covers \Valksor\Component\DoctrineTools\Doctrine\Migrations\VersionComparatorWithoutNamespace
 */
final class VersionComparatorWithoutNamespaceTest extends TestCase
{
    public function testCompareIgnoresNamespacePrefix(): void
    {
        $comparator = new VersionComparatorWithoutNamespace();

        $older = new Version('App\\Migrations\\Version20240101000000');
        $newer = new Version('Company\\Module\\Version20240201000000');

        self::assertLessThan(0, $comparator->compare($older, $newer));
    }

    public function testCompareOrdersVersionsAlphabeticallyBySuffix(): void
    {
        $comparator = new VersionComparatorWithoutNamespace();

        $left = new Version('Company\\Module\\VersionB');
        $right = new Version('Another\\Namespace\\VersionA');

        self::assertGreaterThan(0, $comparator->compare($left, $right));
    }
}
