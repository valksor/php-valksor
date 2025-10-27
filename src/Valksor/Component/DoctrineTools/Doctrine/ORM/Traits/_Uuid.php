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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid;

trait _Uuid
{
    #[ORM\Column(type: UuidType::NAME, unique: true, options: ['default' => 'gen_random_uuid()'])]
    private ?Uid\Uuid $uuid = null;

    public function getUuid(): ?Uid\Uuid
    {
        return $this->uuid;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (null === $this->uuid) {
            $this->uuid = Uid\Uuid::v4();
        }
    }

    public function setUuid(
        ?Uid\Uuid $uuid,
    ): static {
        $this->uuid = $uuid;

        return $this;
    }
}
