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

namespace Valksor\Functions\Text\Traits;

use Valksor\Functions\Text\Functions;

use function str_replace;

trait _LatinToCyrillic
{
    public function latinToCyrillic(
        string $text,
        array $search = Functions::MAP_LATIN,
        array $replace = Functions::MAP_CYRILLIC,
    ): string {
        return str_replace(search: $search, replace: $replace, subject: $text);
    }
}
