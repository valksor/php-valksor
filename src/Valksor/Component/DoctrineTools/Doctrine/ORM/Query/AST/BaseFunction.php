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

namespace Valksor\Component\DoctrineTools\Doctrine\ORM\Query\AST;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

use function count;
use function vsprintf;

abstract class BaseFunction extends FunctionNode
{
    protected string $functionPrototype;
    protected array $nodes = [];
    protected array $nodesMapping = [];

    public function getSql(
        SqlWalker $sqlWalker,
    ): string {
        $dispatched = [];

        foreach ($this->nodes as $node) {
            $dispatched[] = null === $node ? 'null' : $node->dispatch($sqlWalker);
        }

        return vsprintf($this->functionPrototype, $dispatched);
    }

    /**
     * @throws QueryException
     */
    public function parse(
        Parser $parser,
    ): void {
        $this->customFunction();

        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->feedParserWithNodes($parser);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    abstract protected function customFunction(): void;

    protected function addNodeMapping(
        string $parserMethod,
    ): void {
        $this->nodesMapping[] = $parserMethod;
    }

    /**
     * @throws QueryException
     */
    protected function feedParserWithNodes(
        Parser $parser,
    ): void {
        for ($i = 0, $count = count($this->nodesMapping); $i < $count; $i++) {
            $this->nodes[$i] = $parser->{$this->nodesMapping[$i]}();

            if ($i < $count - 1) {
                $parser->match(TokenType::T_COMMA);
            }
        }
    }

    protected function setFunctionPrototype(
        string $functionPrototype,
    ): void {
        $this->functionPrototype = $functionPrototype;
    }
}
