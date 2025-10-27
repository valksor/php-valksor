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

namespace Valksor\Functions\Preg;

final class Functions
{
    use Traits\_AddUtf8Modifier;
    use Traits\_Match;
    use Traits\_MatchAll;
    use Traits\_NewPregException;
    use Traits\_RemoveUtf8Modifier;
    use Traits\_Replace;
    use Traits\_ReplaceCallback;
    use Traits\_Split;
}
