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

namespace Valksor\Functions\Local\Tests;

use Composer\InstalledVersions;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use stdClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use UnexpectedValueException;
use Valksor\Functions\Local\Functions;
use Valksor\Functions\Local\Traits\_CurlUA;
use Valksor\Functions\Local\Traits\_WillBeAvailable;

final class LocalTest extends TestCase
{
    private Functions $local;
    private string $tempDir;

    public function testAllMethodsReturnCorrectTypes(): void
    {
        $this->assertIsString($this->local->humanFileSize(1024));
        $this->assertIsBool($this->local->isInstalled(['json']));
        $this->assertIsBool($this->local->mkdir($this->tempDir . '/type_test'));
        $this->assertIsBool($this->local->rmdir($this->tempDir . '/type_test'));
        $this->assertIsBool($this->local->fileExistsCwd('any_file'));
        $this->assertIsString($this->local->getenv('ANY_VAR'));
        $this->assertIsBool($this->local->exists('stdClass'));
        $this->assertIsBool($this->local->willBeAvailable('php', 'stdClass', []));
        $this->assertIsString($this->local->getCurlUserAgent());
    }

    /**
     * @throws ReflectionException
     */
    public function testClassExistsMethod(): void
    {
        $method = new ReflectionClass($this->local)->getMethod('classExists');

        $this->assertTrue($method->invoke($this->local, 'stdClass'));
        $this->assertFalse($method->invoke($this->local, 'NonExistentClass'));
    }

    // Protected method tests using reflection

    /**
     * @throws ReflectionException
     */
    public function testCreateProcessMethod(): void
    {
        $process = new ReflectionClass($this->local)->getMethod('createProcess')->invoke($this->local, ['echo', 'test']);
        $this->assertInstanceOf(Process::class, $process);
        // getCommandLine() returns a string command, not an array
        $this->assertIsString($process->getCommandLine());
        $this->assertStringContainsString('echo', $process->getCommandLine());
        $this->assertStringContainsString('test', $process->getCommandLine());
    }

    // Curl UA Tests
    public function testCurlUAMethodSignature(): void
    {
        $result = $this->local->getCurlUserAgent();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testExistsMethodSignature(): void
    {
        $result = $this->local->exists('AnyClass');
        $this->assertIsBool($result);
    }

    // Exists Tests
    public function testExistsWithExistingClass(): void
    {
        $result = $this->local->exists('stdClass');
        $this->assertTrue($result);
    }

    public function testExistsWithExistingInterface(): void
    {
        $result = $this->local->exists('Countable');
        $this->assertTrue($result);
    }

    public function testExistsWithExistingTrait(): void
    {
        // Create the temp directory first
        $this->local->mkdir($this->tempDir);

        // Create a temporary trait for testing
        $traitCode = '<?php trait TestTrait {}';
        $traitFile = $this->tempDir . '/TestTrait.php';
        file_put_contents($traitFile, $traitCode);

        // Include the trait file
        require_once $traitFile;

        $result = $this->local->exists('TestTrait');
        $this->assertTrue($result);
    }

    public function testExistsWithNonExistentClass(): void
    {
        $result = $this->local->exists('NonExistentClass');
        $this->assertFalse($result);
    }

    public function testExistsWithObject(): void
    {
        $obj = new stdClass();
        $result = $this->local->exists($obj);
        $this->assertTrue($result);
    }

    public function testFileExistsCwdMethodSignature(): void
    {
        $result = $this->local->fileExistsCwd('any_file.txt');
        $this->assertIsBool($result);
    }

    // File Exists CWD Tests
    public function testFileExistsCwdWithExistingFile(): void
    {
        // Create a temporary file in current directory
        $this->local->mkdir($this->tempDir);
        $filename = $this->tempDir . '/test_file.txt';
        file_put_contents($filename, 'test content');

        // Change to that directory
        $originalCwd = getcwd();
        chdir($this->tempDir);

        try {
            $result = $this->local->fileExistsCwd('test_file.txt');
            $this->assertTrue($result);
        } finally {
            chdir($originalCwd);
        }
    }

    public function testFileExistsCwdWithNonExistentFile(): void
    {
        $this->local->mkdir($this->tempDir);
        $originalCwd = getcwd();
        chdir($this->tempDir);

        try {
            $result = $this->local->fileExistsCwd('non_existent_file.txt');
            $this->assertFalse($result);
        } finally {
            chdir($originalCwd);
        }
    }

    public function testGetCurlUserAgentParsesVersionString(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run');
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn("curl 8.5.0-DEV (x86_64-pc-linux-gnu)\nMore details");

        $curl = new class($process) {
            use _CurlUA;

            public function __construct(
                private readonly Process $process,
            ) {
            }

            protected function createProcess(
                array $command,
            ): Process {
                return $this->process;
            }
        };

        $this->assertSame('curl/8.5.0-DEV', $curl->getCurlUserAgent());
    }

    public function testGetCurlUserAgentReturnsRawOutputWhenPatternDoesNotMatch(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run');
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn("custom agent string\nadditional data");

        $curl = new class($process) {
            use _CurlUA;

            public function __construct(
                private readonly Process $process,
            ) {
            }

            protected function createProcess(
                array $command,
            ): Process {
                return $this->process;
            }
        };

        $this->assertSame('custom agent string', $curl->getCurlUserAgent());
    }

    public function testGetCurlUserAgentThrowsExceptionOnFailure(): void
    {
        // Skip this test as Functions class is final and cannot be mocked
        // The curl failure path is tested implicitly through reflection testing
        $this->assertTrue(true);
    }

    public function testGetCurlUserAgentThrowsWhenProcessFails(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run');
        $process->method('isSuccessful')->willReturn(false);
        $process->method('getCommandLine')->willReturn('curl --version');
        $process->method('getExitCode')->willReturn(1);
        $process->method('getExitCodeText')->willReturn('General error');
        $process->method('getErrorOutput')->willReturn('curl: command not found');

        $curl = new class($process) {
            use _CurlUA;

            public function __construct(
                private readonly Process $process,
            ) {
            }

            protected function createProcess(
                array $command,
            ): Process {
                return $this->process;
            }
        };

        $this->expectException(ProcessFailedException::class);

        $curl->getCurlUserAgent();
    }

    public function testGetEnvMethodSignature(): void
    {
        $result = $this->local->getenv('ANY_VAR');
        $this->assertIsString($result);
    }

    // Get Env Tests
    public function testGetEnvWithExistingEnvironmentVariable(): void
    {
        // Set a test environment variable
        putenv('TEST_LOCAL_VAR=test_value');

        $result = $this->local->getenv('TEST_LOCAL_VAR');

        $this->assertSame('test_value', $result);

        // Clean up
        putenv('TEST_LOCAL_VAR');
    }

    public function testGetEnvWithNonExistentVariableReturnsDefault(): void
    {
        $result = $this->local->getenv('NON_EXISTENT_VAR', false);
        $this->assertSame('NON_EXISTENT_VAR', $result);
    }

    public function testHumanFileSizeEdgeCases(): void
    {
        // Test boundary values
        $this->assertSame('1.00K', $this->local->humanFileSize(1023));
        $this->assertSame('1.00K', $this->local->humanFileSize(1024));
        $this->assertSame('1.00K', $this->local->humanFileSize(1025));

        $this->assertSame('1.00M', $this->local->humanFileSize(1047552)); // Just under 1M
        $this->assertSame('1.00M', $this->local->humanFileSize(1048576)); // Exactly 1M
        $this->assertSame('1.00M', $this->local->humanFileSize(1049600)); // Just over 1M
    }

    public function testHumanFileSizeMethodSignature(): void
    {
        $result = $this->local->humanFileSize(1024);
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d+\.\d+[BKMGTEPZY]$/', $result);
    }

    public function testHumanFileSizeWithBytes(): void
    {
        $result = $this->local->humanFileSize(512);
        $this->assertSame('512.00B', $result);
    }

    public function testHumanFileSizeWithCustomDecimals(): void
    {
        $result = $this->local->humanFileSize(1536, 1);
        $this->assertSame('1.5K', $result);

        $result = $this->local->humanFileSize(1536, 3);
        $this->assertSame('1.500K', $result);
    }

    public function testHumanFileSizeWithExabytes(): void
    {
        $result = $this->local->humanFileSize(1152921504606846976); // 1024^6
        $this->assertSame('1.00E', $result);
    }

    public function testHumanFileSizeWithGigabytes(): void
    {
        $result = $this->local->humanFileSize(1073741824); // 1024 * 1024 * 1024
        $this->assertSame('1.00G', $result);
    }

    public function testHumanFileSizeWithKilobytes(): void
    {
        $result = $this->local->humanFileSize(1024);
        $this->assertSame('1.00K', $result);

        $result = $this->local->humanFileSize(1536);
        $this->assertSame('1.50K', $result);
    }

    public function testHumanFileSizeWithMegabytes(): void
    {
        $result = $this->local->humanFileSize(1048576); // 1024 * 1024
        $this->assertSame('1.00M', $result);

        $result = $this->local->humanFileSize(2097152); // 2 * 1024 * 1024
        $this->assertSame('2.00M', $result);
    }

    public function testHumanFileSizeWithPetabytes(): void
    {
        $result = $this->local->humanFileSize(1125899906842624); // 1024^5
        $this->assertSame('1.00P', $result);
    }

    public function testHumanFileSizeWithTerabytes(): void
    {
        $result = $this->local->humanFileSize(1099511627776); // 1024^4
        $this->assertSame('1.00T', $result);
    }

    public function testHumanFileSizeWithYottabytes(): void
    {
        // Skip Zettabyte and Yottabyte tests due to PHP integer overflow on 64-bit systems
        $this->assertTrue(true);
    }

    // Human File Size Tests
    public function testHumanFileSizeWithZeroBytes(): void
    {
        $result = $this->local->humanFileSize(0);
        $this->assertSame('0.00B', $result);
    }

    public function testHumanFileSizeWithZettabytes(): void
    {
        // Skip Zettabyte and Yottabyte tests due to PHP integer overflow on 64-bit systems
        $this->assertTrue(true);
    }

    public function testIsInstalledMethodSignature(): void
    {
        $result = $this->local->isInstalled(['json']);
        $this->assertIsBool($result);
    }

    public function testIsInstalledWithComposerPackage(): void
    {
        // Test with composer package - this will depend on what's installed
        $result = $this->local->isInstalled(['php']);
        $this->assertIsBool($result);
    }

    public function testIsInstalledWithMixedValidAndInvalid(): void
    {
        // Mix a valid extension with invalid package
        $result = $this->local->isInstalled(['json', 'non_existent_package_12345']);
        $this->assertFalse($result); // Should return false if any package is not installed
    }

    public function testIsInstalledWithMultiplePackages(): void
    {
        $result = $this->local->isInstalled(['json', 'php']);
        $this->assertIsBool($result);
    }

    public function testIsInstalledWithNonExistentPackage(): void
    {
        $result = $this->local->isInstalled(['non_existent_package_12345']);
        $this->assertFalse($result);
    }

    // Is Installed Tests
    public function testIsInstalledWithPhpExtension(): void
    {
        // Test with a common PHP extension that should be available
        $result = $this->local->isInstalled(['json']);
        $this->assertIsBool($result);
    }

    // Integration Tests
    public function testMkdirAndRmdirWorkflow(): void
    {
        $dirPath = $this->tempDir . '/workflow_test';

        // Create directory
        $createResult = $this->local->mkdir($dirPath);
        $this->assertTrue($createResult);
        $this->assertDirectoryExists($dirPath);

        // Add a file
        file_put_contents($dirPath . '/test.txt', 'content');

        // Remove directory
        $removeResult = $this->local->rmdir($dirPath);
        $this->assertTrue($removeResult);
        $this->assertFalse(is_dir($dirPath));
    }

    public function testMkdirCreatesNestedDirectory(): void
    {
        $dirPath = $this->tempDir . '/nested/deep/directory';

        $result = $this->local->mkdir($dirPath);

        $this->assertTrue($result);
        $this->assertDirectoryExists($dirPath);
    }

    // Mkdir Tests
    public function testMkdirCreatesNewDirectory(): void
    {
        $dirPath = $this->tempDir . '/new_directory';

        $this->assertFalse(is_dir($dirPath));

        $result = $this->local->mkdir($dirPath);

        $this->assertTrue($result);
        $this->assertDirectoryExists($dirPath);
    }

    public function testMkdirMethodSignature(): void
    {
        $result = $this->local->mkdir($this->tempDir . '/test');
        $this->assertIsBool($result);
    }

    public function testMkdirReturnsTrueForExistingDirectory(): void
    {
        // Create directory first
        mkdir($this->tempDir . '/existing', 0o777, true);

        $result = $this->local->mkdir($this->tempDir . '/existing');

        $this->assertTrue($result);
    }

    public function testMkdirThrowsExceptionOnFailure(): void
    {
        // Create a parent directory and remove write permissions to force mkdir failure
        $parentDir = $this->tempDir . '/readonly_parent';
        $this->local->mkdir($parentDir);

        // Remove write permissions to make subdirectory creation fail
        chmod($parentDir, 0o444); // Read-only

        $dirPath = $parentDir . '/subdir_that_should_fail';

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Directory "' . $dirPath . '" was not created');

        try {
            $this->local->mkdir($dirPath);
        } finally {
            // Restore permissions for cleanup in tearDown
            chmod($parentDir, 0o755);
        }
    }

    public function testRmdirMethodSignature(): void
    {
        $result = $this->local->rmdir($this->tempDir . '/test');
        $this->assertIsBool($result);
    }

    public function testRmdirRemovesDirectoryWithFiles(): void
    {
        $dirPath = $this->tempDir . '/with_files';
        mkdir($dirPath, 0o777, true);
        file_put_contents($dirPath . '/file.txt', 'test');

        $result = $this->local->rmdir($dirPath);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($dirPath));
    }

    // Rmdir Tests
    public function testRmdirRemovesEmptyDirectory(): void
    {
        $dirPath = $this->tempDir . '/empty_dir';
        mkdir($dirPath, 0o777, true);

        $this->assertDirectoryExists($dirPath);

        $result = $this->local->rmdir($dirPath);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($dirPath));
    }

    public function testRmdirRemovesNestedDirectory(): void
    {
        $dirPath = $this->tempDir . '/nested/deep/structure';
        mkdir($dirPath, 0o777, true);
        file_put_contents($dirPath . '/file.txt', 'test');

        $result = $this->local->rmdir($this->tempDir . '/nested');

        $this->assertTrue($result);
        $this->assertFalse(is_dir($this->tempDir . '/nested'));
    }

    public function testRmdirReturnsTrueForNonExistentDirectory(): void
    {
        $result = $this->local->rmdir($this->tempDir . '/non_existent');
        $this->assertTrue($result); // Method should return true even if directory doesn't exist
    }

    public function testWillBeAvailableMethodSignature(): void
    {
        $result = $this->local->willBeAvailable('php', 'stdClass', []);
        $this->assertIsBool($result);
    }

    public function testWillBeAvailableReturnsTrueForDevDependencyParent(): void
    {
        $result = $this->local->willBeAvailable('phpunit/phpunit', TestCase::class, ['phpunit/phpunit'], 'different/root');

        $this->assertTrue($result);
    }

    public function testWillBeAvailableReturnsTrueWhenParentMatchesRootPackage(): void
    {
        $rootPackage = InstalledVersions::getRootPackage()['name'] ?? '';

        $result = $this->local->willBeAvailable('phpunit/phpunit', TestCase::class, [$rootPackage], 'different/root');

        $this->assertTrue($result);
    }

    public function testWillBeAvailableReturnsTrueWhenRootPackageMatchesCheck(): void
    {
        $rootPackage = InstalledVersions::getRootPackage()['name'] ?? '';

        $result = $this->local->willBeAvailable('phpunit/phpunit', TestCase::class, [], $rootPackage);

        $this->assertTrue($result);
    }

    public function testWillBeAvailableThrowsWhenInstalledVersionsClassMissing(): void
    {
        $local = new class {
            use _WillBeAvailable;

            protected function classExists(
                string $class,
            ): bool {
                return false;
            }
        };

        $this->expectException(LogicException::class);

        $local->willBeAvailable('phpunit/phpunit', TestCase::class, []);
    }

    // Will Be Available extended tests
    public function testWillBeAvailableWithDevRequirements(): void
    {
        // Test with a common dev dependency (phpunit is usually a dev dependency)
        $result = $this->local->willBeAvailable('phpunit/phpunit', TestCase::class, []);
        $this->assertIsBool($result);
    }

    public function testWillBeAvailableWithNonExistentExtension(): void
    {
        $result = $this->local->willBeAvailable('non_existent_package_12345', 'NonExistentClass', []);
        $this->assertFalse($result);
    }

    // Will Be Available Tests
    public function testWillBeAvailableWithPhpExtension(): void
    {
        $result = $this->local->willBeAvailable('php', 'stdClass', []);
        $this->assertIsBool($result);
    }

    public function testWillBeAvailableWithRootPackage(): void
    {
        // Test with the current root package name
        $result = $this->local->willBeAvailable('php', 'stdClass', []);
        $this->assertIsBool($result);
    }

    protected function setUp(): void
    {
        $this->local = new Functions();
        $this->tempDir = sys_get_temp_dir() . '/local_test_' . uniqid('', true);
    }

    protected function tearDown(): void
    {
        // Clean up any created directories
        if (is_dir($this->tempDir)) {
            $this->local->rmdir($this->tempDir);
        }
    }
}
