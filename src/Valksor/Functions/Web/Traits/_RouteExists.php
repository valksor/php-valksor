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

namespace Valksor\Functions\Web\Traits;

use Symfony\Component\Routing\RouterInterface;

trait _RouteExists
{
    public function routeExists(
        RouterInterface $router,
        string $route,
    ): bool {
        return null !== $router->getRouteCollection()->get(name: $route);
    }
}
