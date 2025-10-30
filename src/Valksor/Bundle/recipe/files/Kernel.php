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

namespace Valksor\Bundle\recipe\files;

use Valksor\Bundle\Kernel\AbstractKernel;

class Kernel extends AbstractKernel
{
    public function __construct(
        string $environment,
        bool $debug,
        string $id,
    ) {
        $this->infrastructure = $_ENV['VALKSOR_INFRASTRUCTURE_DIR'] ?? 'infrastructure';
        $this->apps = $_ENV['VALKSOR_APPS_DIR'] ?? 'apps';

        parent::__construct($environment, $debug, $id);
    }
}
