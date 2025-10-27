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

namespace Valksor\Functions\Local\Traits;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function explode;
use function preg_match;
use function trim;

trait _CurlUA
{
    public function getCurlUserAgent(): string
    {
        $process = $this->createProcess(['curl', '--version']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = trim(explode("\n", $process->getOutput(), 2)[0]);

        if (preg_match('/curl\s+([\d.]+(?:-(?:DEV|alpha\d+|beta\d+|rc\d+|[A-Za-z0-9#]+))?)(?:\s|\()/', $output, $matches)) {
            return 'curl/' . $matches[1];
        }

        return $output;
    }

    protected function createProcess(
        array $command,
    ): Process {
        return new Process($command);
    }
}
