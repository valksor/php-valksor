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

namespace Valksor\Component\DoctrineTools\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionAddExtensionPGCrypto extends AbstractMigration
{
    public function down(
        Schema $schema,
    ): void {
        $this->addSql('DROP EXTENSION IF EXISTS pgcrypto');
    }

    public function getDescription(): string
    {
        return 'CREATE EXTENSION IF NOT EXISTS pgcrypto';
    }

    public function up(
        Schema $schema,
    ): void {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pgcrypto');
    }
}
