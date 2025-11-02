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

use Valksor\Bundle\Tests\Fixtures\JsonFileStub;

$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException('Composer autoload file not found. Run "composer install" first.');
}

require $autoloadPath;

$fixturePath = __DIR__ . '/Fixtures/JsonFileStub.php';
$composerJsonFile = 'Composer\Json\JsonFile';
$fixtureClass = JsonFileStub::class;

if (!class_exists($composerJsonFile, autoload: false) && is_file($fixturePath)) {
    require $fixturePath;

    class_alias($fixtureClass, $composerJsonFile);
}

$dotenvStub = __DIR__ . '/Fixtures/DotenvStub.php';

if (!class_exists('Symfony\\Component\\Dotenv\\Dotenv') && is_file($dotenvStub)) {
    require $dotenvStub;
}
