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

use Throwable;

use function bin2hex;
use function random_bytes;
use function substr;

trait _UniqueId
{
    public function uniqueId(
        int $length = 32,
    ): string {
        try {
            return substr(string: bin2hex(string: random_bytes(length: $length)), offset: 0, length: $length);
        } catch (Throwable) {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _RandomString;
                };
            }

            return $_helper->randomString(length: $length);
        }
    }
}
