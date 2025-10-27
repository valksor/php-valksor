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

use Doctrine\ORM\Mapping as ORM;

trait _IsActive
{
    #[ORM\Column(options: ['default' => false])]
    private bool $isActive = false;

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(
        bool $isActive,
    ): static {
        $this->isActive = $isActive;

        return $this;
    }
}
