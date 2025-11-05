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

namespace Valksor\XlsxParser\Exception;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Throwable;
use ZipArchive;

use function array_flip;
use function array_key_exists;
use function sprintf;

final class InvalidArchiveException extends RuntimeException
{
    public function __construct(
        int $code,
        ?Throwable $previous = null,
    ) {
        parent::__construct('Error opening file: ' . $this->getErrorMessage($code), previous: $previous);
    }

    private function getErrorMessage(
        int $errorCode,
    ): string {
        return sprintf('An error has occured: %s::%s (%d)', ZipArchive::class, $this->getZipErrorString($errorCode), $errorCode);
    }

    private function getZipErrorString(
        int $value,
    ): string {
        $map = array_flip(new ReflectionClass(ZipArchive::class)->getConstants(ReflectionProperty::IS_PUBLIC));

        return array_key_exists($value, $map) ? $map[$value] : 'UNKNOWN';
    }
}
