<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\ClassLeak;

use ECSPrefix202609\PhpParser\NodeTraverser;
use ECSPrefix202609\PhpParser\Parser;
use ECSPrefix202609\TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use ECSPrefix202609\TomasVotruba\ClassLeak\NodeVisitor\ConstructorParamTypeNodeVisitor;
/**
 * @see \TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\ConstructorParamTypeResolverTest
 */
final class ConstructorParamTypeResolver
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
        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return [];
        }
        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);
        $constructorParamTypeNodeVisitor = new ConstructorParamTypeNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($constructorParamTypeNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $constructorParamTypeNodeVisitor->getParamTypeNames();
    }
}
