<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Component\DoctrineTools\Doctrine\ORM\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait _Version
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Version]
    protected ?int $version = null;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(
        ?int $version,
    ): static {
        $this->version = $version;

        return $this;
    }
}
