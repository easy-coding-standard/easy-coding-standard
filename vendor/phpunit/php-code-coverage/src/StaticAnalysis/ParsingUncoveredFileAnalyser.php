<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis;

use ECSPrefix20210804\PhpParser\Error;
use ECSPrefix20210804\PhpParser\Lexer;
use ECSPrefix20210804\PhpParser\NodeTraverser;
use ECSPrefix20210804\PhpParser\ParserFactory;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ParsingUncoveredFileAnalyser implements \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\UncoveredFileAnalyser
{
    public function executableLinesIn(string $filename) : array
    {
        $parser = (new \ECSPrefix20210804\PhpParser\ParserFactory())->create(\ECSPrefix20210804\PhpParser\ParserFactory::PREFER_PHP7, new \ECSPrefix20210804\PhpParser\Lexer());
        try {
            $nodes = $parser->parse(\file_get_contents($filename));
            \assert($nodes !== null);
            $traverser = new \ECSPrefix20210804\PhpParser\NodeTraverser();
            $visitor = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\ExecutableLinesFindingVisitor();
            $traverser->addVisitor($visitor);
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            return $visitor->executableLines();
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210804\PhpParser\Error $error) {
        }
        // @codeCoverageIgnoreEnd
        return [];
    }
}
