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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tmpPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST, ) as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());

                continue;
            }

            unlink($file->getRealPath());
        }

        rmdir($this->tmpPath);
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
