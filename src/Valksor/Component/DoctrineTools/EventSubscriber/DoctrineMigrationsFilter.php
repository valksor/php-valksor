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

namespace Valksor\Component\DoctrineTools\EventSubscriber;

use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Valksor\Functions\Local;

#[AutoconfigureTag('doctrine.dbal.schema_filter')]
class DoctrineMigrationsFilter implements EventSubscriberInterface
{
    private bool $enabled = true;

    public function __invoke(
        AbstractAsset|string $asset,
    ): bool {
        if (!$this->enabled) {
            return true;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_Exists;
            };
        }

        if (!$_helper->exists(TableMetadataStorageConfiguration::class)) {
            return true;
        }

        if ($asset instanceof AbstractAsset) {
            $asset = $asset->getName();
        }

        return $asset !== new TableMetadataStorageConfiguration()->getTableName();
    }

    public function onConsoleCommand(
        ConsoleCommandEvent $event,
    ): void {
        $command = $event->getCommand();

        if (null === $command) {
            return;
        }

        /*
         * Any console commands from the Doctrine Migrations bundle may attempt
         * to initialize migrations information storage table. Because of this
         * they should not be affected by this filter because their logic may
         * get broken since they will not "see" the table, they may try to use
         */
        if ($command instanceof DoctrineCommand) {
            $this->enabled = false;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
        ];
    }
}
