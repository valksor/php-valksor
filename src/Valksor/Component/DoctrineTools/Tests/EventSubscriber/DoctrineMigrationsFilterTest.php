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

namespace Valksor\Component\DoctrineTools\Tests\EventSubscriber;

use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Valksor\Component\DoctrineTools\EventSubscriber\DoctrineMigrationsFilter;

/**
 * @covers \Valksor\Component\DoctrineTools\EventSubscriber\DoctrineMigrationsFilter
 */
final class DoctrineMigrationsFilterTest extends TestCase
{
    public function testFilterDisablesAfterDoctrineConsoleCommand(): void
    {
        $this->requireDoctrineMigrations();

        $filter = new DoctrineMigrationsFilter();
        $tableName = new TableMetadataStorageConfiguration()->getTableName();

        $command = $this->createStub(DoctrineCommand::class);
        $event = new ConsoleCommandEvent($command, new ArrayInput([]), new NullOutput());

        $filter->onConsoleCommand($event);

        self::assertTrue($filter($tableName));
    }

    public function testHandlesDoctrineAbstractAssets(): void
    {
        $this->requireDoctrineMigrations();

        $filter = new DoctrineMigrationsFilter();
        $tableName = new TableMetadataStorageConfiguration()->getTableName();

        $table = new Table($tableName);
        self::assertFalse($filter($table));
    }

    public function testSkipsDoctrineMigrationsMetadataTable(): void
    {
        $this->requireDoctrineMigrations();

        $filter = new DoctrineMigrationsFilter();
        $tableName = new TableMetadataStorageConfiguration()->getTableName();

        self::assertFalse($filter($tableName));
        self::assertTrue($filter('another_table'));
    }

    private function isDoctrineMigrationsAvailable(): bool
    {
        return class_exists('Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration');
    }

    private function requireDoctrineMigrations(): void
    {
        if (!$this->isDoctrineMigrationsAvailable()) {
            self::markTestSkipped('Doctrine Migrations component not available');
        }
    }
}
