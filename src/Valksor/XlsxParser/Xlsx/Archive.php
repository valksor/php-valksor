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

namespace Valksor\XlsxParser\Xlsx;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Valksor\XlsxParser\Exception\InvalidArchiveException;
use ZipArchive;

use function file_exists;
use function is_dir;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @internal
 */
final class Archive
{
    private string $tmpPath;
    private ?ZipArchive $zip = null;

    public function __construct(
        private readonly string $archivePath,
    ) {
        $tmpDir = sys_get_temp_dir();

        if (is_dir('/dev/shm')) {
            $tmpDir = '/dev/shm';
        }

        $this->tmpPath = tempnam($tmpDir, 'valksor_xlsx_parser_archive');
        unlink($this->tmpPath);
    }

    public function __destruct()
    {
        $this->deleteTmp();
        $this->closeArchive();
    }

    public function extract(
        string $filePath,
    ): string {
        $tmpPath = sprintf('%s/%s', $this->tmpPath, $filePath);

        if (!file_exists($tmpPath)) {
            $this->getArchive()->extractTo($this->tmpPath, $filePath);
        }

        return $tmpPath;
    }

    private function closeArchive(): void
    {
        $this->zip?->close();
        $this->zip = null;
    }

    private function deleteTmp(): void
    {
        if (!is_dir($this->tmpPath)) {
            return;
        }

        foreach ($this->generateFilesToDelete($this->tmpPath) as $file) {
            if ($file instanceof SplFileInfo) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            } else {
                // Handle the root directory (string path)
                rmdir($file);
            }
        }
    }

    /**
     * Generator that yields files and directories for deletion in proper order.
     *
     * @param string $path The path to clean up
     *
     * @return Generator<SplFileInfo|string> Yields files first, then directories, ending with the root path
     */
    private function generateFilesToDelete(
        string $path,
    ): Generator {
        yield from new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        // Finally yield the directory itself
        yield $path;
    }

    private function getArchive(): ZipArchive
    {
        if (null === $this->zip) {
            $this->zip = new ZipArchive();
            $error = $this->zip->open($this->archivePath);

            if (true !== $error) {
                $this->zip = null;

                throw new InvalidArchiveException($error);
            }
        }

        return $this->zip;
    }
}
