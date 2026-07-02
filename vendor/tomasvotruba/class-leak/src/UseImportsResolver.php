<?php

declare (strict_types=1);
namespace ECSPrefix202607\TomasVotruba\ClassLeak;

use ECSPrefix202607\PhpParser\NodeTraverser;
use ECSPrefix202607\PhpParser\Parser;
use RuntimeException;
use Throwable;
use ECSPrefix202607\TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use ECSPrefix202607\TomasVotruba\ClassLeak\NodeVisitor\UsedClassNodeVisitor;
/**
 * @see \TomasVotruba\ClassLeak\Tests\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator
     */
    private $fullyQualifiedNameNodeDecorator;
    public function __construct(Parser $parser, FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
    {
        $this->parser = $parser;
        $this->fullyQualifiedNameNodeDecorator = $fullyQualifiedNameNodeDecorator;
    }
    /**
     * @return string[]
     */
    public function resolve(string $filePath): array
    {
        /** @var string $fileContents */
        $fileContents = file_get_contents($filePath);
        try {
            $stmts = $this->parser->parse($fileContents);
            if ($stmts === null) {
                return [];
            }
        } catch (Throwable $throwable) {
            throw new RuntimeException(sprintf('Could not parse file "%s": %s', $filePath, $throwable->getMessage()), $throwable->getCode(), $throwable);
        }
        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);
        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $usedClassNodeVisitor->getUsedNames();
    }
}
