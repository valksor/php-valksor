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

namespace Valksor\Functions\Local;

final class Functions
{
    use Traits\_CurlUA;
    use Traits\_Exists;
    use Traits\_FileExistsCwd;
    use Traits\_GetEnv;
    use Traits\_HumanFileSize;
    use Traits\_IsInstalled;
    use Traits\_MkDir;
    use Traits\_RmDir;
    use Traits\_WillBeAvailable;
}
