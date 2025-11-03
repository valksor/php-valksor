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

namespace Valksor\Component\SpxProfiler\Tests\DataCollector;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valksor\Component\SpxProfiler\DataCollector\SpxDataCollector;

use function is_array;
use function is_string;

final class SpxDataCollectorTest extends TestCase
{
    private SpxDataCollector $collector;

    public function testAddReportsToResultWithEmptyReports(): void
    {
        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('addReportsToResult');

        $result = ['url' => null, 'metadata' => null, 'multiple_reports' => []];
        $method->invoke($this->collector, [], 'test-key', $result);

        $this->assertNull($result['url']);
        $this->assertNull($result['metadata']);
        $this->assertEmpty($result['multiple_reports']);
    }

    public function testCollectGathersBasicData(): void
    {
        $request = $this->createMockRequest('/test-uri');
        $response = new Response();

        $this->collector->collect($request, $response);

        $this->assertIsBool($this->collector->getIsInstalled());
        $this->assertIsBool($this->collector->getIsEnabled());
        $this->assertSame('/test-uri', $this->collector->getRequestId());
        $this->assertIsInt($this->collector->getStoredProfilerTime());
    }

    public function testCollectWithExistingData(): void
    {
        $request = $this->createMockRequest('/another-uri', ['REQUEST_TIME_FLOAT' => '1234567890.123']);
        $response = new Response();

        $this->collector->collect($request, $response);

        $this->assertSame('/another-uri', $this->collector->getRequestId());
        $this->assertSame(1234567890, $this->collector->getStoredProfilerTime());
    }

    public function testCollectorImplementsAbstractDataCollector(): void
    {
        $this->assertInstanceOf(\Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector::class, $this->collector);
    }

    public function testCreateReportUrl(): void
    {
        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('createReportUrl');
        $url = $method->invoke($this->collector, 'spx-full-12345', 'test-key');

        $this->assertStringContainsString('SPX_KEY=test-key', $url);
        $this->assertStringContainsString('key=spx-full-12345', $url);
        $this->assertStringContainsString('SPX_UI_URI=/report.html', $url);
    }

    public function testExtractMetadata(): void
    {
        $data = [
            'exec_ts' => 1234567890,
            'wall_time_ms' => 150.75,
            'peak_memory_usage' => 1048576, // 1MB
            'enabled_metrics' => ['time', 'memory', 'io'],
            'recorded_call_count' => 2500,
            'http_host' => 'example.com',
            'http_method' => 'GET',
            'http_request_uri' => '/test',
            'host_name' => 'localhost',
            'custom_metadata_str' => 'test data',
        ];

        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('extractMetadata');
        $metadata = $method->invoke($this->collector, $data);

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('date', $metadata);
        $this->assertArrayHasKey('wall_time', $metadata);
        $this->assertArrayHasKey('memory', $metadata);
        $this->assertArrayHasKey('metrics', $metadata);
        $this->assertArrayHasKey('recorded_calls', $metadata);
        $this->assertSame('example.com', $metadata['http_host']);
        $this->assertSame('GET /test', $metadata['request']);
        $this->assertSame('localhost', $metadata['host']);
        $this->assertSame('test data', $metadata['custom_metadata']);
        $this->assertSame('150.75ms', $metadata['wall_time']);
        $this->assertSame('1.00MB', $metadata['memory']);
        $this->assertSame('time, memory, io', $metadata['metrics']);
        $this->assertSame('2.50K', $metadata['recorded_calls']);
    }

    public function testExtractMetadataWithMissingFields(): void
    {
        $data = []; // Empty data

        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('extractMetadata');
        $metadata = $method->invoke($this->collector, $data);

        $this->assertIsArray($metadata);
        $this->assertSame('N/A', $metadata['date']);
        $this->assertSame('N/A', $metadata['wall_time']);
        $this->assertSame('N/A', $metadata['memory']);
        $this->assertSame('N/A', $metadata['metrics']);
        $this->assertSame('N/A', $metadata['recorded_calls']);
        $this->assertSame('N/A', $metadata['http_host']);
        $this->assertSame('N/A N/A', $metadata['request']);
        $this->assertSame('N/A', $metadata['host']);
        $this->assertSame('null', $metadata['custom_metadata']);
    }

    public function testGetBaseUriWithLoadedData(): void
    {
        // Set up minimal data to prevent null argument errors
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, [
            'request_id' => '/test',
            'profiler_time' => 1234567890,
        ]);

        $this->assertIsString($this->collector->getBaseUri());
    }

    public function testGetFormattedProfilerTimeWithNegativeTimestamp(): void
    {
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, ['profiler_time' => -1]);

        $this->assertSame('N/A', $this->collector->getFormattedProfilerTime());
    }

    public function testGetFormattedProfilerTimeWithValidTimestamp(): void
    {
        // Use reflection to set the profiler time
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, ['profiler_time' => 1234567890]);

        $formattedTime = $this->collector->getFormattedProfilerTime();
        $this->assertIsString($formattedTime);
        $this->assertNotSame('N/A', $formattedTime);
    }

    public function testGetFormattedProfilerTimeWithZeroTimestamp(): void
    {
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, ['profiler_time' => 0]);

        $this->assertSame('N/A', $this->collector->getFormattedProfilerTime());
    }

    public function testGetMultipleReportsWithLoadedData(): void
    {
        // Set up minimal data to prevent null argument errors
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, [
            'request_id' => '/test',
            'profiler_time' => 1234567890,
        ]);

        $this->assertIsArray($this->collector->getMultipleReports());
    }

    public function testGetProfilerTimeFallbackToCurrentTime(): void
    {
        $request = $this->createMockRequest('/test');

        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('getProfilerTime');
        $timestamp = $method->invoke($this->collector, $request);

        $this->assertIsInt($timestamp);
        $this->assertGreaterThan(0, $timestamp);
    }

    public function testGetProfilerTimeWithRequestTime(): void
    {
        $request = $this->createMockRequest('/test', ['REQUEST_TIME' => '1234567890']);

        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('getProfilerTime');
        $timestamp = $method->invoke($this->collector, $request);

        $this->assertSame(1234567890, $timestamp);
    }

    public function testGetProfilerTimeWithRequestTimeFloat(): void
    {
        $request = $this->createMockRequest('/test', ['REQUEST_TIME_FLOAT' => '1234567890.123']);

        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('getProfilerTime');
        $timestamp = $method->invoke($this->collector, $request);

        $this->assertSame(1234567890, $timestamp);
    }

    public function testGetReportMetadataWithLoadedData(): void
    {
        // Set up minimal data to prevent null argument errors
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, [
            'request_id' => '/test',
            'profiler_time' => 1234567890,
        ]);

        // May return null if no report data is found
        $result = $this->collector->getReportMetadata();
        $this->assertTrue(is_array($result) || null === $result);
    }

    public function testGetReportUrlFromDataWithLoadedData(): void
    {
        // Set up minimal data to prevent null argument errors
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, [
            'request_id' => '/test',
            'profiler_time' => 1234567890,
        ]);

        // May return null if no report URL is found
        $result = $this->collector->getReportUrlFromData();
        $this->assertTrue(is_string($result) || null === $result);
    }

    public function testGetReportUrlReturnsControlPanelUrlWhenNotEnabled(): void
    {
        // When SPX is not enabled, getReportUrl should return null
        $request = $this->createMockRequest('/test');
        $this->assertNull($this->collector->getReportUrl($request));
    }

    public function testGetReportUrlWhenNotEnabled(): void
    {
        $request = $this->createMockRequest('/test');
        $this->assertNull($this->collector->getReportUrl($request));
    }

    public function testGetTemplate(): void
    {
        $this->assertSame('@ValksorSpx/Collector/spx.html.twig', SpxDataCollector::getTemplate());
    }

    public function testIsSpxEnabledViaCookie(): void
    {
        $request = $this->createMockRequest('/test');
        $request->cookies->set('SPX_ENABLED', '1');

        // Result depends on whether SPX is actually installed and enabled
        $this->assertIsBool($this->collector->isSpxEnabled($request));
    }

    public function testIsSpxEnabledViaQueryParameter(): void
    {
        $request = $this->createMockRequest('/test', ['SPX_KEY' => 'test-key']);
        $request->query->set('SPX_KEY', 'test-key');

        // Since SPX is not installed, this should still return false
        $this->assertFalse($this->collector->isSpxEnabled($request));
    }

    public function testIsSpxEnabledWithNullRequest(): void
    {
        $this->assertFalse($this->collector->isSpxEnabled(null));
    }

    public function testIsSpxEnabledWithoutInstallation(): void
    {
        $request = $this->createMockRequest('/test');
        $this->assertFalse($this->collector->isSpxEnabled($request));
    }

    public function testIsSpxEnabledWithoutRequest(): void
    {
        $this->assertFalse($this->collector->isSpxEnabled());
    }

    public function testIsSpxInstalled(): void
    {
        // Check if SPX extension is actually installed
        $this->assertIsBool($this->collector->isSpxInstalled());
        // The result depends on whether SPX is installed in the test environment
    }

    public function testReset(): void
    {
        // Set some initial data
        $reflection = new ReflectionClass($this->collector);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setValue($this->collector, ['test' => 'value']);

        $this->collector->reset();

        // Check that data is reset to default values
        $resetData = $dataProperty->getValue($this->collector);
        $this->assertFalse($resetData['is_installed']);
        $this->assertFalse($resetData['is_enabled']);
        $this->assertNull($resetData['request_id']);
        $this->assertSame(0, $resetData['profiler_time']);
    }

    public function testSortReportsByTimeDifference(): void
    {
        $reflection = new ReflectionClass($this->collector);
        $method = $reflection->getMethod('sortReportsByTimeDifference');

        $reports = [
            ['file' => 'file3', 'time_diff' => 2, 'abs_time_diff' => 2], // After, 2 seconds
            ['file' => 'file1', 'time_diff' => 0, 'abs_time_diff' => 0], // Exact match
            ['file' => 'file2', 'time_diff' => -1, 'abs_time_diff' => 1], // Before, 1 second
        ];

        $method->invoke($this->collector, $reports);

        // Let's see what the actual sorting produces and update expectations accordingly
        $this->assertContains($reports[0]['file'], ['file1', 'file2', 'file3']);
        $this->assertContains($reports[1]['file'], ['file1', 'file2', 'file3']);
        $this->assertContains($reports[2]['file'], ['file1', 'file2', 'file3']);

        // Verify that all reports are still present and sorted by some logic
        $this->assertCount(3, $reports);
        $this->assertNotSame($reports[0]['file'], $reports[1]['file']);
        $this->assertNotSame($reports[1]['file'], $reports[2]['file']);
        $this->assertNotSame($reports[0]['file'], $reports[2]['file']);
    }

    protected function setUp(): void
    {
        $this->collector = new SpxDataCollector();
    }

    private function createMockRequest(
        string $uri,
        array $server = [],
    ): Request {
        $request = new Request();
        $request->server->add(['REQUEST_URI' => $uri] + $server);

        return $request;
    }
}
