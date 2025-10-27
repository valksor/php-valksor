<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Date\Traits;

use DateTimeImmutable;
use Exception;
use Valksor\Functions\Date\Functions;

trait _FromUnixTimestamp
{
    /**
     * @throws Exception
     */
    public function fromUnixTimestamp(
        int $timestamp = 0,
        ?string $format = null,
    ): string {
        return new DateTimeImmutable()->setTimestamp($timestamp)->format($format ?? Functions::FORMAT);
    }
}
