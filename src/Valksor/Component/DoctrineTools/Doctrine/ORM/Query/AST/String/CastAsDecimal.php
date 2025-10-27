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

namespace Valksor\Component\DoctrineTools\Doctrine\ORM\Query\AST\String;

use Valksor\Component\DoctrineTools\Doctrine\ORM\Query\AST\BaseFunction;

class CastAsDecimal extends BaseFunction
{
    protected function customFunction(): void
    {
        $this->setFunctionPrototype('CAST(REPLACE(%s, \',\', \'.\') AS decimal)');
        $this->addNodeMapping('StringPrimary');
    }
}
