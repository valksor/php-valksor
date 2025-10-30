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

namespace Valksor\Bundle\Runtime;

use Symfony\Component\Dotenv\Dotenv;

use function array_fill_keys;
use function array_filter;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function explode;
use function file_get_contents;
use function implode;
use function in_array;
use function is_bool;
use function is_file;
use function is_numeric;
use function is_string;
use function rtrim;
use function strtolower;
use function trim;

final class AppContextResolver
{
    private const string APP_DEBUG_KEY = 'APP_DEBUG';
    private const string APP_ENV_KEY = 'APP_ENV';

    /**
     * @param array<string, mixed> $context
     */
    public static function isDebugEnabled(
        array $context,
    ): bool {
        return self::toBool($context[self::APP_DEBUG_KEY] ?? false);
    }

    /**
     * Ensures APP_ENV/APP_DEBUG from app-specific .env files override the runtime context.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public static function resolve(
        array $context,
        string $projectDir,
        string $appId,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): array {
        $dotenv = new Dotenv();
        $projectDir = rtrim($projectDir, '/\\');
        $infrastructureDir = $projectDir . '/' . $infrastructureDir;
        $appDir = $projectDir . '/' . $appsDir . '/' . $appId;

        $parsedCache = [];

        $parse = static function (string $path) use ($dotenv, &$parsedCache): array {
            if (array_key_exists($path, $parsedCache)) {
                return $parsedCache[$path];
            }

            if (!is_file($path)) {
                return $parsedCache[$path] = [];
            }

            return $parsedCache[$path] = $dotenv->parse((string) file_get_contents($path), $path);
        };

        $apply = static function (array $values, array &$target): void {
            foreach ($values as $key => $value) {
                $target[$key] = $value;
            }
        };

        $basePaths = [
            $infrastructureDir . '/.env',
            $infrastructureDir . '/.env.local',
            $appDir . '/.env',
            $appDir . '/.env.local',
        ];

        $baseOverrides = [];

        foreach ($basePaths as $path) {
            $apply($parse($path), $baseOverrides);
        }

        $resolvedEnv = $baseOverrides[self::APP_ENV_KEY] ?? null;

        if (null === $resolvedEnv || '' === trim((string) $resolvedEnv)) {
            $candidate = $context[self::APP_ENV_KEY] ?? null;

            if (is_string($candidate)) {
                $candidate = trim($candidate);
            }

            if ('' === ($candidate ?? '') || 'dev' === strtolower((string) $candidate)) {
                $resolvedEnv = 'prod';
            } else {
                $resolvedEnv = (string) $candidate;
            }
        } else {
            $resolvedEnv = trim((string) $resolvedEnv);
        }

        $orderedPaths = [
            $infrastructureDir . '/.env',
            $infrastructureDir . '/.env.' . $resolvedEnv,
            $infrastructureDir . '/.env.local',
            $infrastructureDir . '/.env.' . $resolvedEnv . '.local',
            $appDir . '/.env',
            $appDir . '/.env.' . $resolvedEnv,
            $appDir . '/.env.local',
            $appDir . '/.env.' . $resolvedEnv . '.local',
        ];

        $overrides = [];

        foreach ($orderedPaths as $path) {
            $apply($parse($path), $overrides);
        }

        if (!array_key_exists(self::APP_ENV_KEY, $overrides) || '' === trim((string) $overrides[self::APP_ENV_KEY])) {
            $overrides[self::APP_ENV_KEY] = $resolvedEnv;
        } else {
            $overrides[self::APP_ENV_KEY] = trim((string) $overrides[self::APP_ENV_KEY]);
        }

        if (!array_key_exists(self::APP_DEBUG_KEY, $overrides) || '' === trim((string) $overrides[self::APP_DEBUG_KEY])) {
            $overrides[self::APP_DEBUG_KEY] = 'dev' === strtolower($resolvedEnv) ? '1' : '0';
        }

        $updates = [];

        foreach ([self::APP_ENV_KEY, self::APP_DEBUG_KEY] as $key) {
            if (!array_key_exists($key, $overrides)) {
                continue;
            }

            $value = (string) $overrides[$key];

            if (self::APP_DEBUG_KEY === $key) {
                $value = self::normalizeDebugValue($value);
            } else {
                $value = trim($value);
            }

            $updates[$key] = $value;
            $context[$key] = $value;
        }

        if ([] !== $updates) {
            $dotenvVars = $_SERVER['SYMFONY_DOTENV_VARS'] ?? $_ENV['SYMFONY_DOTENV_VARS'] ?? '';
            $tracked = array_flip(array_filter('' === $dotenvVars ? [] : explode(',', $dotenvVars)));

            $tracked += array_fill_keys(array_keys($updates), true);

            $dotenvVars = implode(',', array_keys($tracked));
            $_ENV['SYMFONY_DOTENV_VARS'] = $_SERVER['SYMFONY_DOTENV_VARS'] = $dotenvVars;

            $dotenv->populate($updates, true);
        }

        return $context;
    }

    private static function normalizeDebugValue(
        string $value,
    ): string {
        return self::toBool($value) ? '1' : '0';
    }

    private static function toBool(
        mixed $value,
    ): bool {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return 0 !== (int) $value;
        }

        if (!is_string($value)) {
            return false;
        }

        $normalized = strtolower(trim($value));

        if ('' === $normalized) {
            return false;
        }

        if (in_array($normalized, ['0', 'false', 'off', 'no'], true)) {
            return false;
        }

        if (in_array($normalized, ['1', 'true', 'on', 'yes'], true)) {
            return true;
        }

        return (bool) $normalized;
    }
}
