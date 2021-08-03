<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\Complexity;

use ECSPrefix20210803\PhpParser\Error;
use ECSPrefix20210803\PhpParser\Lexer;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\NodeTraverser;
use ECSPrefix20210803\PhpParser\NodeVisitor\NameResolver;
use ECSPrefix20210803\PhpParser\NodeVisitor\ParentConnectingVisitor;
use ECSPrefix20210803\PhpParser\Parser;
use ECSPrefix20210803\PhpParser\ParserFactory;
final class Calculator
{
    /**
     * @throws RuntimeException
     */
    public function calculateForSourceFile(string $sourceFile) : \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCollection
    {
        return $this->calculateForSourceString(\file_get_contents($sourceFile));
    }
    /**
     * @throws RuntimeException
     */
    public function calculateForSourceString(string $source) : \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCollection
    {
        try {
            $nodes = $this->parser()->parse($source);
            \assert($nodes !== null);
            return $this->calculateForAbstractSyntaxTree($nodes);
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210803\PhpParser\Error $error) {
            throw new \ECSPrefix20210803\SebastianBergmann\Complexity\RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @param Node[] $nodes
     *
     * @throws RuntimeException
     */
    public function calculateForAbstractSyntaxTree(array $nodes) : \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCollection
    {
        $traverser = new \ECSPrefix20210803\PhpParser\NodeTraverser();
        $complexityCalculatingVisitor = new \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCalculatingVisitor(\true);
        $traverser->addVisitor(new \ECSPrefix20210803\PhpParser\NodeVisitor\NameResolver());
        $traverser->addVisitor(new \ECSPrefix20210803\PhpParser\NodeVisitor\ParentConnectingVisitor());
        $traverser->addVisitor($complexityCalculatingVisitor);
        try {
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210803\PhpParser\Error $error) {
            throw new \ECSPrefix20210803\SebastianBergmann\Complexity\RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
        return $complexityCalculatingVisitor->result();
    }
    private function parser() : \ECSPrefix20210803\PhpParser\Parser
    {
        return (new \ECSPrefix20210803\PhpParser\ParserFactory())->create(\ECSPrefix20210803\PhpParser\ParserFactory::PREFER_PHP7, new \ECSPrefix20210803\PhpParser\Lexer());
    }
}
