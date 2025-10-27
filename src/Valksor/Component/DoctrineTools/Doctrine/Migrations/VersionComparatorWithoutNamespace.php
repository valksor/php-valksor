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

namespace Valksor\Component\DoctrineTools\Doctrine\Migrations;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;

use function array_pop;
use function explode;
use function strcmp;

class VersionComparatorWithoutNamespace implements Comparator
{
    public function compare(
        Version $a,
        Version $b,
    ): int {
        return strcmp($this->versionWithoutNamespace($a), $this->versionWithoutNamespace($b));
    }

    private function versionWithoutNamespace(
        Version $version,
    ): string {
        $path = explode('\\', (string) $version);

        return array_pop($path);
    }
}
