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

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

trait _CreatedUpdated
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @throws Exception
     */
    public function setCreatedAt(
        ?DateTimeImmutable $createdAt,
    ): static {
        $this->createdAt = $this->modifyTimezone(dateTimeImmutable: $createdAt);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setUpdatedAt(
        ?DateTimeImmutable $updatedAt,
    ): static {
        $this->updatedAt = $this->modifyTimezone(dateTimeImmutable: $updatedAt);

        return $this;
    }

    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): static
    {
        $this->setUpdatedAt(updatedAt: new UTCDateTimeImmutable());

        if (null === $this->createdAt) {
            $this->setCreatedAt(createdAt: new UTCDateTimeImmutable());
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function modifyTimezone(
        ?DateTimeImmutable $dateTimeImmutable,
    ): ?DateTimeImmutable {
        if (null !== $dateTimeImmutable) {
            return UTCDateTimeImmutable::createFromInterface(object: $dateTimeImmutable);
        }

        return null;
    }
}
