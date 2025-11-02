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

namespace Symfony\Component\Dotenv;

if (class_exists(Dotenv::class)) {
    return;
}

final class Dotenv
{
    public function load(
        string ...$paths,
    ): array {
        $values = [];

        foreach ($paths as $path) {
            if (!is_file($path)) {
                continue;
            }

            $values += $this->parse((string) file_get_contents($path), $path);
        }

        $this->populate($values, true);

        return $values;
    }

    /**
     * @return array<string, string>
     */
    public function parse(
        string $data,
        string $path = '',
    ): array {
        $variables = [];

        foreach (preg_split('/\r?\n/', $data) as $line) {
            $line = trim($line);

            if ('' === $line || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $variables[trim($key)] = trim($value);
        }

        return $variables;
    }

    /**
     * @param array<string, string> $values
     */
    public function populate(
        array $values,
        bool $overrideExisting = false,
    ): void {
        foreach ($values as $key => $value) {
            if (!$overrideExisting && isset($_ENV[$key])) {
                continue;
            }

            $_ENV[$key] = $_SERVER[$key] = $value;
        }
    }
}
