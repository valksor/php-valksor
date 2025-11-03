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

namespace Valksor\Component\SpxProfiler\DataCollector;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Valksor\Functions\Iteration;
use Valksor\Functions\Text;

use function abs;
use function array_key_exists;
use function array_reverse;
use function count;
use function extension_loaded;
use function file_get_contents;
use function filemtime;
use function glob;
use function implode;
use function ini_get;
use function is_dir;
use function number_format;
use function pathinfo;
use function sprintf;
use function time;
use function usort;

use const GLOB_BRACE;
use const PATHINFO_FILENAME;

class SpxDataCollector extends AbstractDataCollector
{
    /**
     * URL pattern for SPX control panel.
     */
    private const string CONTROL_PANEL_URL_PATTERN = '/?SPX_KEY=%s&SPX_UI_URI=/';

    /**
     * Default SPX data directory.
     */
    private const string DEFAULT_DATA_DIR = '/tmp/spx';

    /**
     * Time threshold for matching reports (in seconds).
     */
    private const int MATCHING_TIME_THRESHOLD = 3;

    /**
     * URL pattern for SPX report.
     */
    private const string REPORT_URL_PATTERN = '/?SPX_KEY=%s&SPX_UI_URI=/report.html&key=%s';

    /**
     * Default timezone for date formatting (UTC+2/3).
     */
    private const string TIMEZONE = 'Europe/Riga';

    private ?string $baseUri = null;

    private ?array $loadedReportData = null;

    public function __construct()
    {
        $this->reset();
    }

    public function collect(
        Request $request,
        Response $response,
        ?Throwable $exception = null,
    ): void {
        $this->data = [
            'is_installed' => $this->isSpxInstalled(),
            'is_enabled' => $this->isSpxEnabled($request),
            'request_id' => $request->getRequestUri(),
            'profiler_time' => $this->getProfilerTime($request),
        ];
    }

    /**
     * @throws Exception
     */
    public function getBaseUri(): ?string
    {
        $this->loadReportData();

        return $this->baseUri;
    }

    /**
     * Get the formatted profiler time with the correct timezone.
     *
     * @throws DateMalformedStringException
     */
    public function getFormattedProfilerTime(): string
    {
        $timestamp = $this->getStoredProfilerTime();

        if ($timestamp <= 0) {
            return 'N/A';
        }

        $dateTime = new DateTime('@' . $timestamp);
        $dateTime->setTimezone(new DateTimeZone(self::TIMEZONE));

        return $dateTime->format('Y-m-d H:i:s');
    }

    public function getIsEnabled(): bool
    {
        return $this->data['is_enabled'] ?? false;
    }

    public function getIsInstalled(): bool
    {
        return $this->data['is_installed'] ?? false;
    }

    /**
     * @throws Exception
     */
    public function getMultipleReports(): array
    {
        $this->loadReportData();

        return $this->loadedReportData['multiple_reports'];
    }

    /**
     * @throws Exception
     */
    public function getReportMetadata(): ?array
    {
        $this->loadReportData();

        return $this->loadedReportData['report_metadata'];
    }

    /**
     * Get the URL for the SPX report.
     */
    public function getReportUrl(
        Request $request,
    ): ?string {
        if (!$this->isSpxEnabled($request)) {
            return null;
        }

        $spxKey = ini_get('spx.http_key');

        $reportKey = $this->findMostRecentReportKey();

        if ($reportKey) {
            return sprintf(self::REPORT_URL_PATTERN, $spxKey, $reportKey);
        }

        return sprintf(self::CONTROL_PANEL_URL_PATTERN, $spxKey);
    }

    /**
     * @throws Exception
     */
    public function getReportUrlFromData(): ?string
    {
        $this->loadReportData();

        return $this->loadedReportData['report_url'];
    }

    public function getRequestId(): ?string
    {
        return $this->data['request_id'] ?? null;
    }

    public function getStoredProfilerTime(): int
    {
        return $this->data['profiler_time'] ?? 0;
    }

    public function isSpxEnabled(
        ?Request $request = null,
    ): bool {
        if (null === $request || !$this->isSpxInstalled()) {
            return false;
        }

        if ('1' === $request->cookies->get('SPX_ENABLED')) {
            return true;
        }

        $spxKey = ini_get('spx.http_key');

        return $spxKey && $request->query->get('SPX_KEY') === $spxKey;
    }

    public function isSpxInstalled(): bool
    {
        return extension_loaded('spx');
    }

    public function reset(): void
    {
        $this->data = [
            'is_installed' => false,
            'is_enabled' => false,
            'request_id' => null,
            'profiler_time' => 0,
        ];
        $this->loadedReportData = null;
        $this->baseUri = null;
    }

    public static function getTemplate(): ?string
    {
        return '@ValksorSpx/Collector/spx.html.twig';
    }

    /**
     * Add report information to the result array.
     *
     * @throws DateMalformedStringException
     */
    private function addReportsToResult(
        array $reports,
        string $spxKey,
        array &$result,
    ): void {
        if (empty($reports)) {
            return;
        }

        // Sort reports by time difference
        $this->sortReportsByTimeDifference($reports);

        // If we found multiple reports, add them to the result
        if (count($reports) > 1) {
            foreach ($reports as $report) {
                $reportKey = pathinfo($report['file'], PATHINFO_FILENAME);
                $result['multiple_reports'][] = [
                    'url' => $this->createReportUrl($reportKey, $spxKey),
                    'metadata' => $this->extractMetadata($report['data']),
                ];
            }
        }

        // Use the closest one as the main report
        $reportKey = pathinfo($reports[0]['file'], PATHINFO_FILENAME);
        $result['url'] = $this->createReportUrl($reportKey, $spxKey);
        $result['metadata'] = $this->extractMetadata($reports[0]['data']);
    }

    /**
     * Create a report URL for the given report key.
     */
    private function createReportUrl(
        string $reportKey,
        string $spxKey,
    ): string {
        return sprintf(self::REPORT_URL_PATTERN, $spxKey, $reportKey);
    }

    /**
     * Extract metadata from a SPX report.
     *
     * @throws DateMalformedStringException
     */
    private function extractMetadata(
        array $data,
    ): array {
        // Format the date from the timestamp
        // Use the same timezone as SPX (UTC+2/3)
        $date = 'N/A';

        if (array_key_exists('exec_ts', $data)) {
            $dateTime = new DateTime('@' . $data['exec_ts']);
            $dateTime->setTimezone(new DateTimeZone(self::TIMEZONE));
            $date = $dateTime->format('Y-m-d H:i:s');
        }

        // Format the wall time (already in milliseconds)
        $wallTime = array_key_exists('wall_time_ms', $data)
            ? number_format($data['wall_time_ms'], 2) . 'ms'
            : 'N/A';

        // Format the memory usage (convert from bytes to MB)
        $memory = array_key_exists('peak_memory_usage', $data)
            ? number_format($data['peak_memory_usage'] / (1024 * 1024), 2) . 'MB'
            : 'N/A';

        // Format the metrics
        $metrics = array_key_exists('enabled_metrics', $data)
            ? implode(', ', $data['enabled_metrics'])
            : 'N/A';

        // Format the recorded calls
        $recordedCalls = array_key_exists('recorded_call_count', $data)
            ? number_format($data['recorded_call_count'] / 1000, 2) . 'K'
            : 'N/A';

        return [
            'date' => $date,
            'http_host' => $data['http_host'] ?? 'N/A',
            'request' => ($data['http_method'] ?? 'N/A') . ' ' . ($data['http_request_uri'] ?? 'N/A'),
            'host' => $data['host_name'] ?? 'N/A',
            'custom_metadata' => $data['custom_metadata_str'] ?? 'null',
            'wall_time' => $wallTime,
            'memory' => $memory,
            'metrics' => $metrics,
            'recorded_calls' => $recordedCalls,
        ];
    }

    private function findMostRecentReportKey(): ?string
    {
        $spxDataDir = ini_get('spx.data_dir') ?: self::DEFAULT_DATA_DIR;

        if (!is_dir($spxDataDir)) {
            return null;
        }

        $files = array_reverse(glob($spxDataDir . '/spx-full-*.json', GLOB_BRACE));

        if (empty($files)) {
            return null;
        }

        // Sort files by modification time, newest first
        usort($files, static fn (string $a, string $b): int => filemtime($b) - filemtime($a));

        return pathinfo($files[0], PATHINFO_FILENAME);
    }

    /**
     * Find a report that matches the given request URI and has a timestamp close to the provided timestamp.
     *
     * @throws JsonException
     * @throws DateMalformedStringException
     */
    private function findReportForRequest(
        string $requestUri,
        int $timestamp = 0,
    ): array {
        $spxDataDir = ini_get('spx.data_dir') ?: self::DEFAULT_DATA_DIR;
        $spxKey = ini_get('spx.http_key');
        $result = ['url' => null, 'metadata' => null, 'multiple_reports' => []];

        if (!is_dir($spxDataDir)) {
            return $result;
        }

        $files = array_reverse(glob($spxDataDir . '/spx-full-*.json', GLOB_BRACE));

        if (empty($files)) {
            return $result;
        }

        // First try to find reports that match the exact request URI
        $matchingReports = $this->processReportFiles($files, $requestUri, $timestamp);

        if (!empty($matchingReports)) {
            $this->addReportsToResult($matchingReports, $spxKey, $result);

            return $result;
        }

        // If no matching report is found, use fallback reports
        $fallbackReports = $this->processReportFiles($files, $requestUri, $timestamp, false);

        if (!empty($fallbackReports)) {
            $this->addReportsToResult($fallbackReports, $spxKey, $result);
        }

        return $result;
    }

    /**
     * Get the timestamp of the profiled request.
     */
    private function getProfilerTime(
        Request $request,
    ): int {
        // Try to get the timestamp from the request using REQUEST_TIME_FLOAT for more accuracy
        $timestamp = $request->server->get('REQUEST_TIME_FLOAT');

        if ($timestamp && (float) $timestamp > 0) {
            return (int) $timestamp;
        }

        // Fallback to REQUEST_TIME
        $timestamp = $request->server->get('REQUEST_TIME');

        if ($timestamp && (int) $timestamp > 0) {
            return (int) $timestamp;
        }

        // Fallback to current time
        return time();
    }

    /**
     * Lazy load the report data when it's actually needed.
     *
     * @throws Exception
     */
    private function loadReportData(): void
    {
        if (null !== $this->loadedReportData) {
            return;
        }

        $request = new Request();
        $request->server->set('REQUEST_URI', $this->data['request_id'] ?? null);

        $reportInfo = $this->findReportForRequest(
            $this->data['request_id'] ?? null,
            $this->data['profiler_time'] ?? 0,
        );

        $spxKey = ini_get('spx.http_key');
        $this->baseUri = sprintf(self::CONTROL_PANEL_URL_PATTERN, $spxKey);

        $this->loadedReportData = [
            'report_url' => $reportInfo['url'] ?? null,
            'report_metadata' => $reportInfo['metadata'] ?? null,
            'multiple_reports' => $reportInfo['multiple_reports'] ?? [],
        ];
    }

    /**
     * Process SPX report files and collect matching reports.
     *
     * @throws JsonException
     */
    private function processReportFiles(
        array $files,
        string $requestUri,
        int $timestamp,
        bool $matchExactUri = true,
    ): array {
        $reports = [];
        $timeThreshold = self::MATCHING_TIME_THRESHOLD;

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (false === $content) {
                continue;
            }

            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use Iteration\Traits\_JsonDecode;
                    use Text\Traits\_StrStartWithAny;
                };
            }

            $data = $_helper->jsonDecode($content, true);

            if (!$data) {
                continue;
            }

            $reportUri = $data['http_request_uri'] ?? '';

            // Skip if we're matching exact URI and it doesn't match
            if ($matchExactUri && $reportUri !== $requestUri) {
                continue;
            }

            // Skip Web Debug Toolbar requests for fallback reports
            if (!$matchExactUri && $_helper->strStartsWithAny($reportUri, ['/_wdt/', '/_profiler/'])) {
                continue;
            }

            // Calculate time difference
            $reportTs = $data['exec_ts'] ?? 0;
            $timeDiff = $reportTs - $timestamp;
            $absTimeDiff = abs($timeDiff);

            // Check if the report is within the time threshold
            if ($absTimeDiff <= $timeThreshold) {
                $reports[] = [
                    'file' => $file,
                    'data' => $data,
                    'exec_ts' => $reportTs,
                    'time_diff' => $timeDiff,
                    'abs_time_diff' => $absTimeDiff,
                ];
            }
        }

        return $reports;
    }

    /**
     * Sort reports by time difference, prioritizing exact matches and reports after the profiler time.
     */
    private function sortReportsByTimeDifference(
        array &$reports,
    ): void {
        usort($reports, static function (array $a, array $b): int {
            // Prioritize exact matches, then +1/-1 second matches
            $aExact = 0 === $a['abs_time_diff'];
            $bExact = 0 === $b['abs_time_diff'];
            $aOneSecond = 1 === $a['abs_time_diff'];
            $bOneSecond = 1 === $b['abs_time_diff'];

            // Exact match comes first
            if ($aExact && !$bExact) {
                return -1; // a comes first
            }

            if (!$aExact && $bExact) {
                return 1; // b comes first
            }

            // Then +/-1 second matches
            if ($aOneSecond && !$bOneSecond) {
                return -1; // a comes first
            }

            if (!$aOneSecond && $bOneSecond) {
                return 1; // b comes first
            }

            // For other cases, use the original logic
            // First, separate reports into those after and before the profiler time
            $aAfter = $a['time_diff'] >= 0;
            $bAfter = $b['time_diff'] >= 0;

            // If one is after and one is before, prioritize the one after
            if ($aAfter && !$bAfter) {
                return -1; // a comes first
            }

            if (!$aAfter && $bAfter) {
                return 1; // b comes first
            }

            // If both are after or both are before, sort by absolute time difference
            return $a['abs_time_diff'] - $b['abs_time_diff'];
        });
    }
}
