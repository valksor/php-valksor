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
use function preg_match_all;

use const PREG_NO_ERROR;
use const PREG_PATTERN_ORDER;

trait _MatchAll
{
    public function matchAll(
        string $pattern,
        string $subject,
        ?array &$matches = null,
        int $flags = PREG_PATTERN_ORDER,
        int $offset = 0,
    ): int {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _AddUtf8Modifier;
                use _NewPregException;
                use _RemoveUtf8Modifier;
            };
        }

        $result = @preg_match_all($_helper->addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);

        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_match_all($_helper->removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);

        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw $_helper->newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, $pattern);
    }
}
