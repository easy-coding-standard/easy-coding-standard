<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\CaseConverter;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\PhpParser\Node\Expr\Variable;
use ConfigTransformer20210601\PhpParser\Node\Scalar\String_;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Expression;
use ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\Service\ServiceOptionNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class AliasCaseConverter implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/BwXkfO/2/
     * @var string
     */
    const ARGUMENT_NAME_REGEX = '#\\$(?<argument_name>\\w+)#';
    /**
     * @see https://regex101.com/r/DDuuVM/1
     * @var string
     */
    const NAMED_ALIAS_REGEX = '#\\w+\\s+\\$\\w+#';
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;
    /**
     * @var ServiceOptionNodeFactory
     */
    private $serviceOptionNodeFactory;
    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory $commonNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory $argsNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\Service\ServiceOptionNodeFactory $serviceOptionNodeFactory, \ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->commonNodeFactory = $commonNodeFactory;
        $this->argsNodeFactory = $argsNodeFactory;
        $this->serviceOptionNodeFactory = $serviceOptionNodeFactory;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }
    public function convertToMethodCall($key, $values) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        if (!\is_string($key)) {
            throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $servicesVariable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::SERVICES);
        if ($this->classLikeExistenceChecker->doesClassLikeExist($key)) {
            return $this->createFromClassLike($key, $values, $servicesVariable);
        }
        // handles: "SomeClass $someVariable: ..."
        $fullClassName = \ConfigTransformer20210601\Nette\Utils\Strings::before($key, ' $');
        if ($fullClassName !== null) {
            $methodCall = $this->createAliasNode($key, $fullClassName, $values);
            return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
        }
        if (\is_string($values) && $values[0] === '@') {
            $args = $this->argsNodeFactory->createFromValues([$values], \true);
            $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($servicesVariable, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS, $args);
            return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
        }
        if (\is_array($values)) {
            return $this->createFromArrayValues($values, $key, $servicesVariable);
        }
        throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    public function match(string $rootKey, $key, $values) : bool
    {
        if ($rootKey !== \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::SERVICES) {
            return \false;
        }
        if (isset($values[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::ALIAS])) {
            return \true;
        }
        if (\ConfigTransformer20210601\Nette\Utils\Strings::match($key, self::NAMED_ALIAS_REGEX)) {
            return \true;
        }
        if (!\is_string($values)) {
            return \false;
        }
        return $values[0] === '@';
    }
    private function createAliasNode(string $key, string $fullClassName, $serviceValues) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        $args = [];
        $classConstFetch = $this->commonNodeFactory->createClassReference($fullClassName);
        \ConfigTransformer20210601\Nette\Utils\Strings::match($key, self::ARGUMENT_NAME_REGEX);
        $argumentName = '$' . \ConfigTransformer20210601\Nette\Utils\Strings::after($key, '$');
        $concat = new \ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat($classConstFetch, new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_(' ' . $argumentName));
        $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg($concat);
        $serviceName = \ltrim($serviceValues, '@');
        $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg(new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($serviceName));
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall(new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::SERVICES), \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS, $args);
    }
    /**
     * @param mixed $values
     */
    private function createFromClassLike(string $key, $values, \ConfigTransformer20210601\PhpParser\Node\Expr\Variable $servicesVariable) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        $classReference = $this->commonNodeFactory->createClassReference($key);
        $argValues = [];
        $argValues[] = $classReference;
        $argValues[] = $values[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS] ?? $values;
        $args = $this->argsNodeFactory->createFromValues($argValues, \true);
        $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($servicesVariable, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS, $args);
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
    }
    private function createFromAlias(string $className, string $key, \ConfigTransformer20210601\PhpParser\Node\Expr\Variable $servicesVariable) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        $classReference = $this->commonNodeFactory->createClassReference($className);
        $args = $this->argsNodeFactory->createFromValues([$key, $classReference]);
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($servicesVariable, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS, $args);
    }
    /**
     * @param mixed[] $values
     */
    private function createFromArrayValues(array $values, string $key, \ConfigTransformer20210601\PhpParser\Node\Expr\Variable $servicesVariable) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        if (isset($values[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS])) {
            $methodCall = $this->createFromAlias($values[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS], $key, $servicesVariable);
            unset($values[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::ALIAS]);
        } else {
            throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        /** @var MethodCall $methodCall */
        $methodCall = $this->serviceOptionNodeFactory->convertServiceOptionsToNodes($values, $methodCall);
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
    }
}
