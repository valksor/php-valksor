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

use Valksor\FullStack;

if (!isset($apps, $infrastructure, $projectDir)) {
    throw new LogicException('Required parameters not set');
}

if (is_dir($projectDir . '/valksor')) {
    $path = '/valksor/src/Valksor/Component/Sse/Resources/assets';
} elseif (class_exists(FullStack::class)) {
    $path = '/vendor/valksor/valksor/src/Valksor/Component/Sse/Resources/assets';
} else {
    $path = '/vendor/valksor/php-sse/Resources/assets';
}

$roots = [
    $infrastructure => [
        'source' => $projectDir . '/' . $infrastructure . '/assets/js',
        'dist' => $projectDir . '/' . $infrastructure . '/assets/dist',
    ],
];

if (is_dir($projectDir . $path)) {
    $roots['valksorsse'] = [
        'source' => $projectDir . $path . '/js',
        'dist' => $projectDir . '/' . $infrastructure . '/assets/dist',
    ];
}

$appId = $_SERVER['APP_KERNEL_NAME'] ?? $_ENV['APP_KERNEL_NAME'] ?? null;

if (is_string($appId) && '' !== $appId) {
    $roots[$appId] = [
        'source' => $projectDir . '/' . $apps . '/' . $appId . '/assets/js',
        'dist' => $projectDir . '/' . $apps . '/' . $appId . '/assets/dist',
    ];
}

$mkdir = static function (
    string $dir,
): bool {
    if (!is_dir(filename: $dir)) {
        if (!mkdir($dir) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        if (!is_dir(filename: $dir)) {
            throw new UnexpectedValueException(message: sprintf('Directory "%s" was not created', $dir));
        }
    }

    return is_dir(filename: $dir);
};

$collectEntries = static function (array $config) use ($projectDir, $mkdir): array {
    $entries = [];

    ['source' => $sourceDir, 'dist' => $distDir] = $config;

    if (!is_dir($sourceDir)) {
        return [];
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || 'js' !== $file->getExtension()) {
            continue;
        }

        $relative = substr($file->getPathname(), strlen($sourceDir) + 1);
        $relative = str_replace('\\', '/', $relative);

        if (!str_ends_with($relative, '.entry.js')) {
            continue;
        }

        $entryName = substr($relative, 0, -strlen('.entry.js'));

        if (is_array($entries) ? array_key_exists($entryName, $entries) : isset($entries[$entryName])) {
            continue;
        }

        $targetPath = $distDir . '/' . $relative;

        if (!is_file($targetPath)) {
            $mkdir(dirname($targetPath));

            @copy($file->getPathname(), $targetPath);
        }

        $targetRelative = substr($targetPath, strlen($projectDir) + 1);
        $targetRelative = str_replace('\\', '/', $targetRelative);

        if (!is_file($targetPath)) {
            // Fallback to source path if the copy failed.
            $targetRelative = substr($file->getPathname(), strlen($projectDir) + 1);
            $targetRelative = str_replace('\\', '/', $targetRelative);
        }

        $entries[$entryName] = './' . $targetRelative;
    }

    return $entries;
};

$entriesByPrefix = array_map($collectEntries, $roots);

$infrastructureEntries = $entriesByPrefix[$infrastructure] ?? [];

$importMap = [];

foreach ($entriesByPrefix as $prefix => $entries) {
    if ($infrastructure === $prefix) {
        foreach ($entries as $name => $path) {
            $importMap[$infrastructure . '/' . $name] = [
                'path' => $path,
                'entrypoint' => true,
            ];
        }

        continue;
    }

    foreach ($infrastructureEntries as $name => $path) {
        $importMap[$prefix . '/' . $name] = [
            'path' => $path,
            'entrypoint' => true,
        ];
    }

    foreach ($entries as $name => $path) {
        $importMap[$prefix . '/' . $name] = [
            'path' => $path,
            'entrypoint' => true,
        ];
    }
}

return $importMap;
