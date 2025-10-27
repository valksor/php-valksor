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

use Valksor\Functions\Text\Functions;

use function str_replace;

trait _CyrillicToLatin
{
    public function cyrillicToLatin(
        string $text,
        array $search = Functions::MAP_CYRILLIC,
        array $replace = Functions::MAP_LATIN,
    ): string {
        return str_replace(search: $search, replace: $replace, subject: $text);
    }
}
