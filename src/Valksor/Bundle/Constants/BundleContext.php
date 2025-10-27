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

namespace Valksor\Bundle\Constants;

enum BundleContext: string
{
    case CALLER_CLASS = 'VALKSOR_BUNDLE_CALL_CLASS';
    case PLURAL = 'VALKSOR_BUNDLE_PLURAL';
    case READ_PROPERTY = 'VALKSOR_BUNDLE_READ_PROPERTY';
    case REFLECTION = 'VALKSOR_BUNDLE_REFLECTION';
}
