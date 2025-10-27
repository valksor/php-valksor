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
use function preg_replace;

use const PREG_NO_ERROR;

trait _Replace
{
    public function replace(
        array|string $pattern,
        string $replacement,
        $subject,
        int $limit = -1,
        ?int &$count = null,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _AddUtf8Modifier;
                use _NewPregException;
                use _RemoveUtf8Modifier;
            };
        }

        $result = @preg_replace($_helper->addUtf8Modifier($pattern), $replacement, $subject, $limit, $count);

        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_replace($_helper->removeUtf8Modifier($pattern), $replacement, $subject, $limit, $count);

        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw $_helper->newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, $pattern);
    }
}
