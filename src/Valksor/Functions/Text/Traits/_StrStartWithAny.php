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

namespace Valksor\Functions\Text\Traits;

trait _StrStartWithAny
{
    public function strStartsWithAny(
        string $haystack,
        array $needles,
    ): bool {
        return (bool) array_filter($needles, static fn ($needle) => str_starts_with($haystack, $needle));
    }
}
