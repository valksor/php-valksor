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

use function array_reverse;
use function implode;
use function mb_str_split;

trait _ReverseUTF8
{
    public function reverseUTF8(
        string $text,
    ): string {
        return implode(separator: '', array: array_reverse(array: mb_str_split(string: $text, encoding: Functions::UTF8)));
    }
}
