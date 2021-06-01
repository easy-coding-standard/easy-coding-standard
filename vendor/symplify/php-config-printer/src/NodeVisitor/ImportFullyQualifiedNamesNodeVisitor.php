<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeVisitor;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Name;
use ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified;
use ConfigTransformer20210601\PhpParser\NodeVisitorAbstract;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Naming\ClassNaming;
final class ImportFullyQualifiedNamesNodeVisitor extends \ConfigTransformer20210601\PhpParser\NodeVisitorAbstract
{
    /**
     * @var ClassNaming
     */
    private $classNaming;
    /**
     * @var string[]
     */
    private $nameImports = [];
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Naming\ClassNaming $classNaming)
    {
        $this->classNaming = $classNaming;
    }
    /**
     * @param Node[] $nodes
     * @return mixed[]|null
     */
    public function beforeTraverse(array $nodes)
    {
        $this->nameImports = [];
        return null;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(\ConfigTransformer20210601\PhpParser\Node $node)
    {
        if (!$node instanceof \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified) {
            return null;
        }
        $fullyQualifiedName = $node->toString();
        // namespace-less class name
        if (\ConfigTransformer20210601\Nette\Utils\Strings::startsWith($fullyQualifiedName, '\\')) {
            $fullyQualifiedName = \ltrim($fullyQualifiedName, '\\');
        }
        if (!\ConfigTransformer20210601\Nette\Utils\Strings::contains($fullyQualifiedName, '\\')) {
            return new \ConfigTransformer20210601\PhpParser\Node\Name($fullyQualifiedName);
        }
        $shortClassName = $this->classNaming->getShortName($fullyQualifiedName);
        $this->nameImports[] = $fullyQualifiedName;
        return new \ConfigTransformer20210601\PhpParser\Node\Name($shortClassName);
    }
    /**
     * @return string[]
     */
    public function getNameImports() : array
    {
        return $this->nameImports;
    }
}
