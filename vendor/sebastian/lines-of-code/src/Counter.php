<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\LinesOfCode;

use function substr_count;
use ECSPrefix20210803\PhpParser\Error;
use ECSPrefix20210803\PhpParser\Lexer;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\NodeTraverser;
use ECSPrefix20210803\PhpParser\Parser;
use ECSPrefix20210803\PhpParser\ParserFactory;
final class Counter
{
    /**
     * @throws RuntimeException
     */
    public function countInSourceFile(string $sourceFile) : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode
    {
        return $this->countInSourceString(\file_get_contents($sourceFile));
    }
    /**
     * @throws RuntimeException
     */
    public function countInSourceString(string $source) : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode
    {
        $linesOfCode = \substr_count($source, "\n");
        if ($linesOfCode === 0 && !empty($source)) {
            $linesOfCode = 1;
        }
        try {
            $nodes = $this->parser()->parse($source);
            \assert($nodes !== null);
            return $this->countInAbstractSyntaxTree($linesOfCode, $nodes);
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210803\PhpParser\Error $error) {
            throw new \ECSPrefix20210803\SebastianBergmann\LinesOfCode\RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @param Node[] $nodes
     *
     * @throws RuntimeException
     */
    public function countInAbstractSyntaxTree(int $linesOfCode, array $nodes) : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode
    {
        $traverser = new \ECSPrefix20210803\PhpParser\NodeTraverser();
        $visitor = new \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LineCountingVisitor($linesOfCode);
        $traverser->addVisitor($visitor);
        try {
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210803\PhpParser\Error $error) {
            throw new \ECSPrefix20210803\SebastianBergmann\LinesOfCode\RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
        return $visitor->result();
    }
    private function parser() : \ECSPrefix20210803\PhpParser\Parser
    {
        return (new \ECSPrefix20210803\PhpParser\ParserFactory())->create(\ECSPrefix20210803\PhpParser\ParserFactory::PREFER_PHP7, new \ECSPrefix20210803\PhpParser\Lexer());
    }
}
