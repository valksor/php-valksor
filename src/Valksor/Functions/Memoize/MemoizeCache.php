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

namespace Valksor\Functions\Memoize;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

use function class_exists;

if (class_exists(Autoconfigure::class)) {
    #[Autoconfigure(public: true, shared: true)]
    class MemoizeCache extends Memoize
    {
    }
} else {
    class MemoizeCache extends Memoize
    {
    }
}
