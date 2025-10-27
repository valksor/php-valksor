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

namespace Valksor\Functions\Text\Traits;

use Symfony\Component\Intl\Countries;

use function mb_strtoupper;

trait _CountryName
{
    public function countryName(
        string $country,
        string $locale = 'en',
    ): string {
        return Countries::getName(country: mb_strtoupper(string: $country), displayLocale: $locale);
    }
}
