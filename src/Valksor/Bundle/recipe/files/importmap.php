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

$projectDir = __DIR__;
$infrastructure = $_ENV['VALKSOR_INFRASTRUCTURE_DIR'] ?? 'infrastructure';
$apps = $_ENV['VALKSOR_APPS_DIR'] ?? 'apps';

return include $projectDir . '/valksor/src/Valksor/Bundle/Resources/importmap.php';
