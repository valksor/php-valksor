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

namespace Valksor\Component\Cache\Tests\Fixtures;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Test double seam: a single interface mirroring the intersection the resilient adapter wraps,
 * so it can be mocked with a plain createMock() call.
 */
interface TagAwareCachePool extends TagAwareAdapterInterface, TagAwareCacheInterface
{
}
