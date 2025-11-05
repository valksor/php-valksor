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

namespace Valksor\XlsxParser\Transformer;

use DateTimeImmutable;
use Valksor\XlsxParser\Xlsx\SharedStrings;
use Valksor\XlsxParser\Xlsx\Styles;

use function filter_var;
use function trim;

use const FILTER_VALIDATE_BOOL;

/**
 * @internal
 */
final class Value
{
    private const string BOOL = 'b';
    private const string EMPTY = '';
    private const string NUMBER = 'n';
    private const string SHARED_STRING = 's';

    private readonly Date $dateTransformer;

    public function __construct(
        private readonly SharedStrings $sharedStrings,
        private readonly Styles $styles,
        ?Date $dateTransformer = null,
    ) {
        $this->dateTransformer = $dateTransformer ?? new Date();
    }

    public function transform(
        string $value,
        string $type,
        string $style,
    ): bool|DateTimeImmutable|float|int|string {
        return match ($type) {
            self::BOOL => filter_var($value, FILTER_VALIDATE_BOOL),
            self::SHARED_STRING => trim($this->sharedStrings->get((int) $value)),
            self::EMPTY, self::NUMBER => $this->transformNumber($style, $value),
            default => trim($value),
        };
    }

    private function transformNumber(
        string $style,
        mixed $value,
    ): DateTimeImmutable|float|int {
        return match (true) {
            $style && Styles::FORMAT_DATE === $this->styles->get((int) $style) => $this->dateTransformer->transform((int) $value),
            default => preg_match('/^\d+\.\d+$/', $value) ? (float) $value : (int) $value,
        };
    }
}
