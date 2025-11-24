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

namespace Valksor\Bundle\Service;

use ReflectionClass;

use function array_merge;

/**
 * PathFilter Helper - Shared filtering utilities.
 *
 * This service provides reusable PathFilter creation and pattern categorization
 * methods used by hot reload, snapshot, and other services that need file filtering.
 * It follows the exact same pattern as HotReloadService for consistency.
 */
class PathFilterHelper
{
    /**
     * Create PathFilter with custom exclusion patterns.
     * Merges default PathFilter exclusions with user-defined patterns.
     */
    public static function createPathFilterWithExclusions(
        array $excludePatterns,
        string $projectDir,
    ): PathFilter {
        // Get default patterns from PathFilter
        $defaultFilter = PathFilter::createDefault($projectDir);
        $defaultExclusions = self::extractDefaultExclusions($defaultFilter);

        // Combine all patterns into a unified list (no categorization needed)
        $allPatterns = array_merge(
            $defaultExclusions['all'],
            $excludePatterns,
        );

        return new PathFilter($allPatterns, $projectDir);
    }

    /**
     * Extract default exclusions from PathFilter using reflection.
     */
    public static function extractDefaultExclusions(
        PathFilter $filter,
    ): array {
        $reflection = new ReflectionClass($filter);
        $excludePatterns = $reflection->getProperty('excludePatterns')->getValue($filter);

        return [
            'directories' => [],
            'globs' => [],
            'filenames' => [],
            'extensions' => [],
            'all' => $excludePatterns,
        ];
    }
}
