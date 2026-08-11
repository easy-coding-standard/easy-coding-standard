<?php

declare (strict_types=1);
namespace ECSPrefix202608\TomasVotruba\ClassLeak;

use ECSPrefix202608\PhpParser\NodeTraverser;
use ECSPrefix202608\PhpParser\Parser;
use ECSPrefix202608\TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use ECSPrefix202608\TomasVotruba\ClassLeak\NodeVisitor\ClassNameNodeVisitor;
use ECSPrefix202608\TomasVotruba\ClassLeak\ValueObject\ClassNames;
/**
 * @see \TomasVotruba\ClassLeak\Tests\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
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
    public function resolveFromFilePath(string $filePath): ?ClassNames
    {
        /** @var string $fileContents */
        $fileContents = file_get_contents($filePath);
        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return null;
        }
        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);
        $classNameNodeVisitor = new ClassNameNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);
        $className = $classNameNodeVisitor->getClassName();
        if (!is_string($className)) {
            return null;
        }
        return new ClassNames($className, $classNameNodeVisitor->hasParentClassOrInterface(), $classNameNodeVisitor->getAttributes());
    }
}
