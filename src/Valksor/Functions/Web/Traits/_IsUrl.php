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

namespace Valksor\Functions\Web\Traits;

use function filter_var;

use const FILTER_SANITIZE_URL;
use const FILTER_VALIDATE_URL;

trait _IsUrl
{
    public function isUrl(
        string $url,
    ): bool {
        return false !== filter_var(value: filter_var(value: $url, filter: FILTER_SANITIZE_URL), filter: FILTER_VALIDATE_URL);
    }
}
