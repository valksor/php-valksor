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

namespace Valksor\Component\DoctrineTools\Doctrine\ORM\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait __None
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING, unique: true)]
    protected ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(
        ?string $id,
    ): static {
        $this->id = $id;

        return $this;
    }
}
