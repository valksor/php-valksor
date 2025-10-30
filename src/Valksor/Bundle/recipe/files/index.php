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

use App\Kernel;
use Valksor\Bundle\Runtime\AppContextResolver;

require_once __DIR__ . '/../vendor/autoload_runtime.php';

return static function (array $context) {
    $projectDir = dirname(__DIR__);

    $appId = null;

    $infrastructure = $_ENV['VALKSOR_INFRASTRUCTURE_DIR'] ?? 'infrastructure';
    $apps = $_ENV['VALKSOR_APPS_DIR'] ?? 'apps';

    if (array_key_exists('APP_KERNEL_NAME', $context)) {
        $appId = $context['APP_KERNEL_NAME'];
    } else {
        $appsDir = __DIR__ . '/../' . $apps;
        $availableAppPaths = glob($appsDir . '/*', GLOB_ONLYDIR);

        if ([] !== $availableAppPaths) {
            $availableApps = $availableAppPaths ? array_map('basename', $availableAppPaths) : [];
            $requestedApp = $_SERVER['HTTP_X_APP_KERNEL_NAME'] ?? '';

            if (in_array($requestedApp, $availableApps, true)) {
                $appId = $requestedApp;
            }
        }
    }

    $context = AppContextResolver::resolve($context, $projectDir, $appId, $apps, $infrastructure);
    $debugEnabled = AppContextResolver::isDebugEnabled($context);

    return new Kernel($context['APP_ENV'], $debugEnabled, $appId);
};
