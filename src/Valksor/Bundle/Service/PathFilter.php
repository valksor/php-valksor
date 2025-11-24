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

use function count;
use function fnmatch;
use function strlen;
use function strtolower;

use const FNM_NOESCAPE;
use const FNM_PATHNAME;

/**
 * Path filtering utility for file system monitoring and build processes.
 *
 * This class provides intelligent file and directory filtering to optimize
 * file watching performance and prevent unnecessary processing of irrelevant
 * files. It's used extensively by the RecursiveInotifyWatcher and build services
 * to focus on source files while ignoring noise.
 *
 * Filtering Strategy:
 * - Directory-based filtering for large dependency folders (node_modules, vendor)
 * - File extension filtering for non-source files (.md, .log, etc.)
 * - Filename filtering for specific configuration files (.gitignore, .gitkeep)
 * - Glob pattern matching for complex path exclusions
 *
 * Performance Benefits:
 * - Reduces inotify watch descriptors by excluding irrelevant directories
 * - Minimizes file system events from build artifacts and dependencies
 * - Improves hot reload responsiveness by focusing on source files only
 * - Prevents infinite loops from watching build output directories
 *
 * Default Ignore Patterns:
 * - Dependencies: node_modules, vendor
 * - Build artifacts: public, var
 * - Development tools: .git, .idea, .webpack-cache
 * - Documentation: *.md files
 * - Git files: .gitignore, .gitkeep
 */
final class PathFilter
{
    /**
     * List of all patterns to ignore during file system traversal.
     * This includes directory names, file extensions, filenames, and glob patterns.
     * Unified approach - any pattern can match both files and directories.
     *
     * @var array<string>
     */
    private readonly array $excludePatterns;

    /**
     * Initialize the path filter with ignore patterns.
     *
     * The constructor receives a unified list of ignore patterns that can match
     * both files and directories. This eliminates artificial categorization and
     * ensures any pattern works regardless of whether it matches a file or directory.
     *
     * @param array  $patterns   List of patterns to ignore (unified approach)
     * @param string $projectDir Project root directory for path normalization
     */
    public function __construct(
        array $patterns,
        /**
         * Project root directory for path normalization.
         *
         * @var string
         */
        private string $projectDir,
    ) {
        $this->excludePatterns = $patterns;
    }

    // ===== BACKWARD COMPATIBILITY METHODS =====
    // These methods provide compatibility with the old categorized PathFilter interface
    // used by HotReloadService and other legacy components.

    /**
     * Get ignored directory patterns from unified patterns.
     *
     * Extracts patterns that are typically directory names (no slashes, no dots, no wildcards).
     *
     * @return array<string> Directory patterns to ignore
     */
    public function getIgnoredDirectories(): array
    {
        $directories = [];

        foreach ($this->excludePatterns as $pattern) {
            $pattern = strtolower(trim($pattern, '/'));

            // Include as directory if:
            // - No dots (not an extension)
            // - No wildcards (not a glob)
            // - Not too short (likely not a filename like 'a' or 'in')
            if (!str_contains($pattern, '.')
                && !str_contains($pattern, '*')
                && !str_contains($pattern, '?')
                && strlen($pattern) > 2
                && !str_starts_with($pattern, '**')) {
                $directories[] = $pattern;
            }
        }

        return $directories;
    }

    /**
     * Get ignored file extension patterns from unified patterns.
     *
     * Extracts patterns that start with a dot and are likely file extensions.
     *
     * @return array<string> File extensions to ignore
     */
    public function getIgnoredExtensions(): array
    {
        $extensions = [];

        foreach ($this->excludePatterns as $pattern) {
            $pattern = strtolower(trim($pattern, '/'));

            // Include as extension if:
            // - No slashes (not a path)
            // - No wildcards (not a glob)
            // - Starts with dot (typical extension format)
            if (!str_contains($pattern, '/')
                && !str_contains($pattern, '*')
                && !str_contains($pattern, '?')
                && str_starts_with($pattern, '.')) {
                $extensions[] = $pattern;
            }
        }

        return $extensions;
    }

    /**
     * Get ignored filename patterns from unified patterns.
     *
     * Extracts patterns that are typical filenames (no slashes, no dots, no wildcards).
     *
     * @return array<string> Filename patterns to ignore
     */
    public function getIgnoredFilenames(): array
    {
        $filenames = [];

        foreach ($this->excludePatterns as $pattern) {
            $pattern = strtolower(trim($pattern, '/'));

            // Include as filename if:
            // - No slashes (not a path)
            // - No dots or starts with dot (not an extension)
            // - No wildcards (not a glob)
            // - Shorter length (typical filenames)
            if (!str_contains($pattern, '/')
                && !str_contains($pattern, '*')
                && !str_contains($pattern, '?')
                && strlen($pattern) <= 10
                && (!str_contains($pattern, '.') || str_starts_with($pattern, '.'))) {
                $filenames[] = $pattern;
            }
        }

        return $filenames;
    }

    /**
     * Get ignored glob patterns from unified patterns.
     *
     * Extracts patterns containing wildcards or glob syntax.
     *
     * @return array<string> Glob patterns to ignore
     */
    public function getIgnoredGlobs(): array
    {
        $globs = [];

        foreach ($this->excludePatterns as $pattern) {
            $pattern = strtolower(trim($pattern, '/'));

            // Include as glob if it contains wildcards
            if (str_contains($pattern, '*') || str_contains($pattern, '?') || str_starts_with($pattern, '**')) {
                $globs[] = $pattern;
            }
        }

        return $globs;
    }

    /**
     * Check if a directory should be ignored during file system traversal.
     *
     * This method uses unified pattern matching - any pattern can match
     * both files and directories, eliminating categorization issues.
     *
     * @param string $basename Directory basename (without path)
     *
     * @return bool True if directory should be ignored, false if it should be watched
     */
    public function shouldIgnoreDirectory(
        string $basename,
    ): bool {
        return $this->matchesAnyPattern($basename);
    }

    /**
     * Check if a file path should be ignored during file system monitoring.
     *
     * This method implements comprehensive path filtering using multiple strategies
     * to determine if a file should trigger build processes. It combines filename
     * matching, extension filtering, and glob pattern matching for maximum flexibility.
     *
     * Filtering Strategy (in order of evaluation):
     * 1. Basic validation for null/empty paths
     * 2. Filename matching for specific files (.gitignore, .gitkeep)
     * 3. Extension filtering for file types (.md, .log, etc.)
     * 4. Glob pattern matching for complex path scenarios
     *
     *
     * Performance Considerations:
     * - Simple checks (filename, extension) are performed first
     * - Expensive glob matching is performed last
     * - Case-insensitive matching for cross-platform compatibility
     *
     * @param string|null $path Full file path to check
     *
     * @return bool True if file should be ignored, false if it should trigger rebuilds
     */
    public function shouldIgnorePath(
        ?string $path,
    ): bool {
        // Basic validation - handle null or empty paths gracefully
        if (null === $path || '' === $path) {
            return false;
        }

        // Convert absolute paths to be relative to project root for pattern matching
        $relativePath = $path;

        // If we have a project directory, try to make the path relative to it
        if (!empty($this->projectDir)) {
            // Direct match - remove project directory prefix
            if (str_starts_with($path, $this->projectDir)) {
                $relativePath = substr($path, strlen($this->projectDir) + 1);
            }
            // If path doesn't start with project dir, use it as-is (already relative)
        }

        // Use unified pattern matching
        return $this->matchesAnyPattern($relativePath);
    }

    public static function createDefault(
        string $projectDir,
    ): self {
        $allPatterns = [
            // Directories and simple patterns
            'node_modules', 'vendor', 'public', 'var', '.git', '.idea', '.webpack-cache',
            '.gitignore', '.gitkeep',

            // File extensions
            '.md',

            // Glob patterns for comprehensive exclusion
            '**/node_modules/**', 'node_modules/**', '**/vendor/**', 'vendor/**',
            '**/public/**', 'public/**', '**/var/**', 'var/**', '**/.git/**', '.git/**',
            '**/.idea/**', '.idea/**', '**/.webpack-cache/**', '.webpack-cache/**',
            '**/*.md', '**/.gitignore', '**/.gitkeep',
        ];

        return new self($allPatterns, $projectDir);
    }

    /**
     * Check if a path matches any exclusion pattern.
     *
     * This unified method eliminates artificial categorization and checks
     * the path against all patterns using appropriate matching strategies.
     *
     * @param string $path Path to check
     *
     * @return bool True if path should be ignored, false otherwise
     */
    private function matchesAnyPattern(
        string $path,
    ): bool {
        $lowerPath = strtolower($path);

        foreach ($this->excludePatterns as $pattern) {
            $lowerPattern = strtolower($pattern);

            // Normalize pattern by removing trailing slashes for matching
            $patternToMatch = rtrim($lowerPattern, '/');
            $hadTrailingSlash = $lowerPattern !== $patternToMatch;

            // Exact match
            if ($lowerPath === $patternToMatch) {
                return true;
            }

            // Directory/component match (for patterns like LICENSE, vendor)
            if ($lowerPath === $patternToMatch  // Exact match
                || str_starts_with($lowerPath, $patternToMatch . '/')) {  // Pattern at start
                return true;
            }

            // For patterns without trailing slashes (like LICENSE), also match nested occurrences
            if (!$hadTrailingSlash && (
                str_contains($lowerPath, '/' . $patternToMatch . '/')
                || str_ends_with($lowerPath, '/' . $patternToMatch)
            )
            ) {
                return true;
            }

            // File extension patterns (like .neon, .md, .lock)
            if (str_starts_with($patternToMatch, '.') && !str_contains($patternToMatch, '/')) {
                if (str_ends_with($lowerPath, $patternToMatch)) {
                    return true;
                }
            }

            // Glob pattern matching
            if (str_contains($pattern, '*') || str_contains($pattern, '?')) {
                if ($this->matchGlobPattern($pattern, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Pattern matching with proper ** wildcard support.
     * Uses simple string matching for common patterns and fnmatch for others.
     */
    private static function matchGlobPattern(
        string $pattern,
        string $path,
    ): bool {
        // Handle common ** patterns with simple string matching for reliability
        if (str_contains($pattern, '**')) {
            // Pattern: **/dirname/** → check if path contains /dirname/ or ends with /dirname
            if (preg_match('#^\*\*/([^/]+)/\*\*$#', $pattern, $matches)) {
                $dirname = $matches[1];

                return str_contains($path, '/' . $dirname . '/')
                       || str_ends_with($path, '/' . $dirname)
                       || $path === $dirname;
            }

            // Pattern: **/*.ext → check if path ends with .ext
            if (preg_match('#^\*\*/\*\.(.+)$#', $pattern, $matches)) {
                return str_ends_with($path, '.' . $matches[1]);
            }

            // Pattern: **/filename → check if path contains /filename/ or ends with /filename
            if (preg_match('#^\*\*/([^*]+)$#', $pattern, $matches)) {
                $filename = $matches[1];

                return str_contains($path, '/' . $filename . '/')
                       || str_ends_with($path, '/' . $filename)
                       || $path === $filename;
            }

            // Pattern: ** → matches everything (shouldn't be used for exclusions)
            if ('**' === $pattern) {
                return true;
            }

            // For other complex ** patterns, fall back to manual matching
            return self::matchRecursivePatternSimple($pattern, $path);
        }

        // For patterns without **, use the standard fnmatch with FNM_PATHNAME
        return fnmatch($pattern, $path, FNM_PATHNAME | FNM_NOESCAPE);
    }

    /**
     * Recursively match pattern parts against path parts.
     */
    private static function matchPatternParts(
        array $patternParts,
        array $pathParts,
        int $pIdx = 0,
        int $pathIdx = 0,
    ): bool {
        // Base case: both exhausted
        if ($pIdx >= count($patternParts) && $pathIdx >= count($pathParts)) {
            return true;
        }

        // Pattern exhausted but path remains
        if ($pIdx >= count($patternParts)) {
            return false;
        }

        $patternPart = $patternParts[$pIdx];

        // Handle ** wildcard
        if ('**' === $patternPart) {
            // Try matching zero parts
            if (self::matchPatternParts($patternParts, $pathParts, $pIdx + 1, $pathIdx)) {
                return true;
            }

            // Try matching one or more parts
            if ($pathIdx < count($pathParts)
                && self::matchPatternParts($patternParts, $pathParts, $pIdx, $pathIdx + 1)) {
                return true;
            }

            return false;
        }

        // Path exhausted but pattern remains
        if ($pathIdx >= count($pathParts)) {
            return false;
        }

        // Match current part
        if (fnmatch($patternPart, $pathParts[$pathIdx], FNM_NOESCAPE)) {
            return self::matchPatternParts($patternParts, $pathParts, $pIdx + 1, $pathIdx + 1);
        }

        return false;
    }

    /**
     * Simple recursive pattern matching for complex ** patterns.
     */
    private static function matchRecursivePatternSimple(
        string $pattern,
        string $path,
    ): bool {
        // Normalize paths
        $pattern = trim($pattern, '/');
        $path = trim($path, '/');

        // Split into parts
        $patternParts = explode('/', $pattern);
        $pathParts = explode('/', $path);

        return self::matchPatternParts($patternParts, $pathParts);
    }
}
