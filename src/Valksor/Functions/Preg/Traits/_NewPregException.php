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

use RuntimeException;
use Valksor\Functions\Preg\Exception\PregPatternException;
use Valksor\Functions\Preg\SkipErrorHandler;

use function is_array;
use function preg_last_error;
use function sprintf;

use const PCRE_VERSION;

trait _NewPregException
{
    public function newPregException(
        int $error,
        string $errorMsg,
        string $method,
        array|string $pattern,
    ): RuntimeException {
        $processPattern = static function (string $pattern) use ($error, $errorMsg, $method): RuntimeException {
            $errorMessage = null;

            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _Match;
                    use _Replace;
                };
            }

            try {
                $result = SkipErrorHandler::execute(static fn () => $_helper->match($pattern, ''));
            } catch (RuntimeException $e) {
                $result = false;
                $errorMessage = $e->getMessage();
            }

            if (false !== $result) {
                return new PregPatternException(sprintf('Unknown error occurred when calling %s: %s.', $method, $errorMsg), $error);
            }

            $code = preg_last_error();

            $message = sprintf(
                '(code: %d) %s',
                $code,
                $_helper->replace('~preg_[a-z_]+[()]{2}: ~', '', $errorMessage),
            );

            return new PregPatternException(
                sprintf('%s(): Invalid PCRE pattern "%s": %s (version: %s)', $method, $pattern, $message, PCRE_VERSION),
                $code,
            );
        };

        if (is_array($pattern)) {
            $exceptions = [];

            foreach ($pattern as $singlePattern) {
                $exceptions[] = $processPattern($singlePattern);
            }

            $combinedMessage = implode("\n", array_map(static fn ($e) => $e->getMessage(), $exceptions));

            return new PregPatternException($combinedMessage, $error);
        }

        return $processPattern($pattern);
    }
}
