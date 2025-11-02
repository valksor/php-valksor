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

namespace Valksor\Bundle\Tests\Fixtures;

final class JsonFileStub
{
    public function __construct(
        private string $path,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function read(): array
    {
        $contents = file_get_contents($this->path);

        if (false === $contents || null === $contents) {
            return [];
        }

        /* @var array<string, mixed> $decoded */
        return json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
    }
}
