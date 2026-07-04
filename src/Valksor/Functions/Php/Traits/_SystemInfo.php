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

namespace Valksor\Functions\Php\Traits;

use Valksor\Functions\Php\Exception\SystemCompatibilityException;

use function php_uname;
use function str_contains;
use function strtolower;

use const PHP_OS_FAMILY;

trait _SystemInfo
{
    /**
     * @return array{os: string, arch: string, extension: string}
     */
    public function normalizeSystemInfo(
        string $osFamily,
        string $machine,
    ): array {
        $architecture = strtolower($machine);

        $normalizedOs = match (strtolower($osFamily)) {
            'windows' => 'windows',
            'darwin' => 'darwin',
            'linux' => 'linux',
            default => throw new SystemCompatibilityException('unsupported OS'),
        };

        $normalizedArch = match (true) {
            str_contains($architecture, 'amd64') || str_contains($architecture, 'x86_64') => 'amd64',
            str_contains($architecture, 'arm64') || str_contains($architecture, 'aarch64') => 'arm64',
            str_contains($architecture, 'i386') || str_contains($architecture, 'i686') => '386',
            default => throw new SystemCompatibilityException('unsupported architecture'),
        };

        return [
            'os' => $normalizedOs,
            'arch' => $normalizedArch,
            'extension' => 'windows' === $normalizedOs ? '.exe' : '',
        ];
    }

    public function systemInfo(): array
    {
        return $this->normalizeSystemInfo(osFamily: PHP_OS_FAMILY, machine: php_uname('m'));
    }
}
