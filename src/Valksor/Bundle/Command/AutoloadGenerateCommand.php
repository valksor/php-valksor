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

namespace Valksor\Bundle\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function basename;
use function explode;
use function file_put_contents;
use function glob;
use function is_dir;
use function lcfirst;
use function sprintf;
use function str_replace;
use function ucfirst;
use function ucwords;

use const GLOB_ONLYDIR;
use const LOCK_EX;

#[AsCommand(name: 'valksor:autoload-generate', description: 'Generate autoloader configuration for apps.')]
class AutoloadGenerateCommand extends AbstractCommand
{
    /**
     * Execute the autoloader generation command.
     */
    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = $this->createSymfonyStyle($input, $output);

        try {
            $appsDir = $this->getAppsDir();
            $autoloadConfig = [];

            if (is_dir($appsDir)) {
                foreach (glob($appsDir . '/*', GLOB_ONLYDIR) as $appDir) {
                    $baseName = basename($appDir);
                    $namespace = lcfirst(str_replace('-', '', ucwords($baseName, '-')));
                    $appName = explode('.', $namespace, 2)[0];
                    $namespaceName = ucfirst($appName);

                    if (is_dir($appsDir . "/$baseName/src/")) {
                        $relativePath = "$baseName/src/";
                        $namespacePrefix = $this->parameterBag->get('valksor.project.autoload.namespace_prefix');
                        $autoloadConfig["$namespacePrefix\\$namespaceName\\"] = $relativePath;
                    }
                }
            }

            $autoloadContent = "<?php declare(strict_types = 1);\n\n";

            $autoloadContent .= "spl_autoload_register(static function (string \$class): void {\n";
            $autoloadContent .= "    \$autoloadConfig = [\n";

            foreach ($autoloadConfig as $namespace => $path) {
                $escapedNamespace = str_replace('\\', '\\\\', $namespace);
                $autoloadContent .= "        '$escapedNamespace' => [__DIR__ . '/$path'],\n";
            }
            $autoloadContent .= "    ];\n\n";
            $autoloadContent .= "    foreach (\$autoloadConfig as \$namespace => \$paths) {\n";
            $autoloadContent .= "        if (str_starts_with(\$class, \$namespace)) {\n";
            $autoloadContent .= "            \$relativeClass = substr(\$class, strlen(\$namespace));\n";
            $autoloadContent .= "            \$file = \$paths[0] . str_replace('\\\\', '/', \$relativeClass) . '.php';\n";
            $autoloadContent .= "            \n";
            $autoloadContent .= "            if (is_file(\$file)) {\n";
            $autoloadContent .= "                require_once \$file;\n";
            $autoloadContent .= "            }\n";
            $autoloadContent .= "        }\n";
            $autoloadContent .= "    }\n";
            $autoloadContent .= "});\n";

            $outputFile = $appsDir . '/autoload.php';

            if (false === file_put_contents($outputFile, $autoloadContent, LOCK_EX)) {
                return $this->handleCommandError(sprintf('Failed to write autoloader to %s', $outputFile), $io);
            }

            $io->success('Generated autoloader configuration for apps:');

            foreach ($autoloadConfig as $namespace => $path) {
                $io->text("  $namespace -> $path");
            }

            return $this->handleCommandSuccess('Autoloader generated successfully!', $io);
        } catch (RuntimeException $exception) {
            return $this->handleCommandError($exception->getMessage(), $io);
        }
    }
}
