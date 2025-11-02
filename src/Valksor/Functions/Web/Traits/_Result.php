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

namespace Valksor\Functions\Web\Traits;

use CURLFile;
use ReflectionException;
use Valksor\Functions\Php\Traits\_Array;

use function array_merge;
use function is_array;
use function is_object;
use function sprintf;

trait _Result
{
    /**
     * @throws ReflectionException
     */
    public function result(
        array $result,
        string $key,
        mixed $value,
    ): array {
        if (!$value instanceof CURLFile && (is_array(value: $value) || is_object(value: $value))) {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _Array;
                };
            }

            $queryResult = [];

            foreach ($_helper->array(input: $value) as $subKey => $subValue) {
                $newKey = sprintf('%s[%s]', $key, $subKey);
                $queryResult = $this->result(result: $queryResult, key: $newKey, value: $subValue);
            }

            return array_merge($result, $queryResult);
        }

        $result[$key] = $value;

        return $result;
    }
}
