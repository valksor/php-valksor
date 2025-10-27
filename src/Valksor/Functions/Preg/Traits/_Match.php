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

namespace Valksor\Functions\Preg\Traits;

use function preg_last_error;
use function preg_last_error_msg;
use function preg_match;

use const PREG_NO_ERROR;

trait _Match
{
    public function match(
        string $pattern,
        string $subject,
        ?array &$matches = null,
        int $flags = 0,
        int $offset = 0,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _AddUtf8Modifier;
                use _NewPregException;
                use _RemoveUtf8Modifier;
            };
        }

        $result = @preg_match($_helper->addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);

        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return 1 === $result;
        }

        $result = @preg_match($_helper->removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);

        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return 1 === $result;
        }

        throw $_helper->newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, $pattern);
    }
}
