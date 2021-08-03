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
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis;

use function array_unique;
use function assert;
use function file_get_contents;
use function is_array;
use function sprintf;
use function substr_count;
use function token_get_all;
use function trim;
use ECSPrefix20210803\PhpParser\Error;
use ECSPrefix20210803\PhpParser\Lexer;
use ECSPrefix20210803\PhpParser\NodeTraverser;
use ECSPrefix20210803\PhpParser\NodeVisitor\NameResolver;
use ECSPrefix20210803\PhpParser\NodeVisitor\ParentConnectingVisitor;
use ECSPrefix20210803\PhpParser\ParserFactory;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\ParserException;
use ECSPrefix20210803\SebastianBergmann\LinesOfCode\LineCountingVisitor;
use ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ParsingCoveredFileAnalyser implements \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CoveredFileAnalyser
{
    /**
     * @var array
     */
    private $classes = [];
    /**
     * @var array
     */
    private $traits = [];
    /**
     * @var array
     */
    private $functions = [];
    /**
     * @var LinesOfCode[]
     */
    private $linesOfCode = [];
    /**
     * @var array
     */
    private $ignoredLines = [];
    /**
     * @var bool
     */
    private $useAnnotationsForIgnoringCode;
    /**
     * @var bool
     */
    private $ignoreDeprecatedCode;
    public function __construct(bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode)
    {
        $this->useAnnotationsForIgnoringCode = $useAnnotationsForIgnoringCode;
        $this->ignoreDeprecatedCode = $ignoreDeprecatedCode;
    }
    public function classesIn(string $filename) : array
    {
        $this->analyse($filename);
        return $this->classes[$filename];
    }
    public function traitsIn(string $filename) : array
    {
        $this->analyse($filename);
        return $this->traits[$filename];
    }
    public function functionsIn(string $filename) : array
    {
        $this->analyse($filename);
        return $this->functions[$filename];
    }
    public function linesOfCodeFor(string $filename) : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode
    {
        $this->analyse($filename);
        return $this->linesOfCode[$filename];
    }
    public function ignoredLinesFor(string $filename) : array
    {
        $this->analyse($filename);
        return $this->ignoredLines[$filename];
    }
    /**
     * @throws ParserException
     */
    private function analyse(string $filename) : void
    {
        if (isset($this->classes[$filename])) {
            return;
        }
        $source = \file_get_contents($filename);
        $linesOfCode = \substr_count($source, "\n");
        if ($linesOfCode === 0 && !empty($source)) {
            $linesOfCode = 1;
        }
        $parser = (new \ECSPrefix20210803\PhpParser\ParserFactory())->create(\ECSPrefix20210803\PhpParser\ParserFactory::PREFER_PHP7, new \ECSPrefix20210803\PhpParser\Lexer());
        try {
            $nodes = $parser->parse($source);
            \assert($nodes !== null);
            $traverser = new \ECSPrefix20210803\PhpParser\NodeTraverser();
            $codeUnitFindingVisitor = new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CodeUnitFindingVisitor();
            $lineCountingVisitor = new \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LineCountingVisitor($linesOfCode);
            $ignoredLinesFindingVisitor = new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\IgnoredLinesFindingVisitor($this->useAnnotationsForIgnoringCode, $this->ignoreDeprecatedCode);
            $traverser->addVisitor(new \ECSPrefix20210803\PhpParser\NodeVisitor\NameResolver());
            $traverser->addVisitor(new \ECSPrefix20210803\PhpParser\NodeVisitor\ParentConnectingVisitor());
            $traverser->addVisitor($codeUnitFindingVisitor);
            $traverser->addVisitor($lineCountingVisitor);
            $traverser->addVisitor($ignoredLinesFindingVisitor);
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            // @codeCoverageIgnoreStart
        } catch (\ECSPrefix20210803\PhpParser\Error $error) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\ParserException(\sprintf('Cannot parse %s: %s', $filename, $error->getMessage()), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
        $this->classes[$filename] = $codeUnitFindingVisitor->classes();
        $this->traits[$filename] = $codeUnitFindingVisitor->traits();
        $this->functions[$filename] = $codeUnitFindingVisitor->functions();
        $this->linesOfCode[$filename] = $lineCountingVisitor->result();
        $this->ignoredLines[$filename] = [];
        $this->findLinesIgnoredByLineBasedAnnotations($filename, $source, $this->useAnnotationsForIgnoringCode);
        $this->ignoredLines[$filename] = \array_unique(\array_merge($this->ignoredLines[$filename], $ignoredLinesFindingVisitor->ignoredLines()));
        \sort($this->ignoredLines[$filename]);
    }
    private function findLinesIgnoredByLineBasedAnnotations(string $filename, string $source, bool $useAnnotationsForIgnoringCode) : void
    {
        $ignore = \false;
        $stop = \false;
        foreach (\token_get_all($source) as $token) {
            if (!\is_array($token)) {
                continue;
            }
            switch ($token[0]) {
                case \T_COMMENT:
                case \T_DOC_COMMENT:
                    if (!$useAnnotationsForIgnoringCode) {
                        break;
                    }
                    $comment = \trim($token[1]);
                    if ($comment === '// @codeCoverageIgnore' || $comment === '//@codeCoverageIgnore') {
                        $ignore = \true;
                        $stop = \true;
                    } elseif ($comment === '// @codeCoverageIgnoreStart' || $comment === '//@codeCoverageIgnoreStart') {
                        $ignore = \true;
                    } elseif ($comment === '// @codeCoverageIgnoreEnd' || $comment === '//@codeCoverageIgnoreEnd') {
                        $stop = \true;
                    }
                    break;
            }
            if ($ignore) {
                $this->ignoredLines[$filename][] = $token[2];
                if ($stop) {
                    $ignore = \false;
                    $stop = \false;
                }
            }
        }
    }
}
