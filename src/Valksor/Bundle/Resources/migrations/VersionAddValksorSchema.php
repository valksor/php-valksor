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

namespace Valksor\Bundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionAddValksorSchema extends AbstractMigration
{
    public function down(
        Schema $schema,
    ): void {
        $this->addSql('DROP SCHEMA IF EXISTS valksor');
    }

    public function getDescription(): string
    {
        return 'CREATE SCHEMA IF NOT EXISTS valksor';
    }

    public function up(
        Schema $schema,
    ): void {
        $this->addSql('CREATE SCHEMA IF NOT EXISTS valksor');
    }
}
