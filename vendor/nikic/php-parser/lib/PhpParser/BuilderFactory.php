<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser;

use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat;
use ConfigTransformer20210601\PhpParser\Node\Identifier;
use ConfigTransformer20210601\PhpParser\Node\Name;
use ConfigTransformer20210601\PhpParser\Node\Scalar\String_;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Use_;
class BuilderFactory
{
    /**
     * Creates a namespace builder.
     *
     * @param null|string|Node\Name $name Name of the namespace
     *
     * @return Builder\Namespace_ The created namespace builder
     */
    public function namespace($name) : \ConfigTransformer20210601\PhpParser\Builder\Namespace_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Namespace_($name);
    }
    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     *
     * @return Builder\Class_ The created class builder
     */
    public function class(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Class_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Class_($name);
    }
    /**
     * Creates an interface builder.
     *
     * @param string $name Name of the interface
     *
     * @return Builder\Interface_ The created interface builder
     */
    public function interface(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Interface_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Interface_($name);
    }
    /**
     * Creates a trait builder.
     *
     * @param string $name Name of the trait
     *
     * @return Builder\Trait_ The created trait builder
     */
    public function trait(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Trait_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Trait_($name);
    }
    /**
     * Creates a trait use builder.
     *
     * @param Node\Name|string ...$traits Trait names
     *
     * @return Builder\TraitUse The create trait use builder
     */
    public function useTrait(...$traits) : \ConfigTransformer20210601\PhpParser\Builder\TraitUse
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\TraitUse(...$traits);
    }
    /**
     * Creates a trait use adaptation builder.
     *
     * @param Node\Name|string|null  $trait  Trait name
     * @param Node\Identifier|string $method Method name
     *
     * @return Builder\TraitUseAdaptation The create trait use adaptation builder
     */
    public function traitUseAdaptation($trait, $method = null) : \ConfigTransformer20210601\PhpParser\Builder\TraitUseAdaptation
    {
        if ($method === null) {
            $method = $trait;
            $trait = null;
        }
        return new \ConfigTransformer20210601\PhpParser\Builder\TraitUseAdaptation($trait, $method);
    }
    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     *
     * @return Builder\Method The created method builder
     */
    public function method(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Method
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Method($name);
    }
    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     *
     * @return Builder\Param The created parameter builder
     */
    public function param(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Param
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Param($name);
    }
    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     *
     * @return Builder\Property The created property builder
     */
    public function property(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Property
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Property($name);
    }
    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     *
     * @return Builder\Function_ The created function builder
     */
    public function function(string $name) : \ConfigTransformer20210601\PhpParser\Builder\Function_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Function_($name);
    }
    /**
     * Creates a namespace/class use builder.
     *
     * @param Node\Name|string $name Name of the entity (namespace or class) to alias
     *
     * @return Builder\Use_ The created use builder
     */
    public function use($name) : \ConfigTransformer20210601\PhpParser\Builder\Use_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Use_($name, \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_NORMAL);
    }
    /**
     * Creates a function use builder.
     *
     * @param Node\Name|string $name Name of the function to alias
     *
     * @return Builder\Use_ The created use function builder
     */
    public function useFunction($name) : \ConfigTransformer20210601\PhpParser\Builder\Use_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Use_($name, \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_FUNCTION);
    }
    /**
     * Creates a constant use builder.
     *
     * @param Node\Name|string $name Name of the const to alias
     *
     * @return Builder\Use_ The created use const builder
     */
    public function useConst($name) : \ConfigTransformer20210601\PhpParser\Builder\Use_
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\Use_($name, \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_CONSTANT);
    }
    /**
     * Creates a class constant builder.
     *
     * @param string|Identifier                          $name  Name
     * @param Node\Expr|bool|null|int|float|string|array $value Value
     *
     * @return Builder\ClassConst The created use const builder
     */
    public function classConst($name, $value) : \ConfigTransformer20210601\PhpParser\Builder\ClassConst
    {
        return new \ConfigTransformer20210601\PhpParser\Builder\ClassConst($name, $value);
    }
    /**
     * Creates node a for a literal value.
     *
     * @param Expr|bool|null|int|float|string|array $value $value
     *
     * @return Expr
     */
    public function val($value) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        return \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($value);
    }
    /**
     * Creates variable node.
     *
     * @param string|Expr $name Name
     *
     * @return Expr\Variable
     */
    public function var($name) : \ConfigTransformer20210601\PhpParser\Node\Expr\Variable
    {
        if (!\is_string($name) && !$name instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            throw new \LogicException('Variable name must be string or Expr');
        }
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable($name);
    }
    /**
     * Normalizes an argument list.
     *
     * Creates Arg nodes for all arguments and converts literal values to expressions.
     *
     * @param array $args List of arguments to normalize
     *
     * @return Arg[]
     */
    public function args(array $args) : array
    {
        $normalizedArgs = [];
        foreach ($args as $arg) {
            if ($arg instanceof \ConfigTransformer20210601\PhpParser\Node\Arg) {
                $normalizedArgs[] = $arg;
            } else {
                $normalizedArgs[] = new \ConfigTransformer20210601\PhpParser\Node\Arg(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($arg));
            }
        }
        return $normalizedArgs;
    }
    /**
     * Creates a function call node.
     *
     * @param string|Name|Expr $name Function name
     * @param array            $args Function arguments
     *
     * @return Expr\FuncCall
     */
    public function funcCall($name, array $args = []) : \ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeNameOrExpr($name), $this->args($args));
    }
    /**
     * Creates a method call node.
     *
     * @param Expr                   $var  Variable the method is called on
     * @param string|Identifier|Expr $name Method name
     * @param array                  $args Method arguments
     *
     * @return Expr\MethodCall
     */
    public function methodCall(\ConfigTransformer20210601\PhpParser\Node\Expr $var, $name, array $args = []) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($var, \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeIdentifierOrExpr($name), $this->args($args));
    }
    /**
     * Creates a static method call node.
     *
     * @param string|Name|Expr       $class Class name
     * @param string|Identifier|Expr $name  Method name
     * @param array                  $args  Method arguments
     *
     * @return Expr\StaticCall
     */
    public function staticCall($class, $name, array $args = []) : \ConfigTransformer20210601\PhpParser\Node\Expr\StaticCall
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\StaticCall(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeNameOrExpr($class), \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeIdentifierOrExpr($name), $this->args($args));
    }
    /**
     * Creates an object creation node.
     *
     * @param string|Name|Expr $class Class name
     * @param array            $args  Constructor arguments
     *
     * @return Expr\New_
     */
    public function new($class, array $args = []) : \ConfigTransformer20210601\PhpParser\Node\Expr\New_
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\New_(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeNameOrExpr($class), $this->args($args));
    }
    /**
     * Creates a constant fetch node.
     *
     * @param string|Name $name Constant name
     *
     * @return Expr\ConstFetch
     */
    public function constFetch($name) : \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeName($name));
    }
    /**
     * Creates a property fetch node.
     *
     * @param Expr                   $var  Variable holding object
     * @param string|Identifier|Expr $name Property name
     *
     * @return Expr\PropertyFetch
     */
    public function propertyFetch(\ConfigTransformer20210601\PhpParser\Node\Expr $var, $name) : \ConfigTransformer20210601\PhpParser\Node\Expr\PropertyFetch
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\PropertyFetch($var, \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeIdentifierOrExpr($name));
    }
    /**
     * Creates a class constant fetch node.
     *
     * @param string|Name|Expr  $class Class name
     * @param string|Identifier $name  Constant name
     *
     * @return Expr\ClassConstFetch
     */
    public function classConstFetch($class, $name) : \ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeNameOrExpr($class), \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeIdentifier($name));
    }
    /**
     * Creates nested Concat nodes from a list of expressions.
     *
     * @param Expr|string ...$exprs Expressions or literal strings
     *
     * @return Concat
     */
    public function concat(...$exprs) : \ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat
    {
        $numExprs = \count($exprs);
        if ($numExprs < 2) {
            throw new \LogicException('Expected at least two expressions');
        }
        $lastConcat = $this->normalizeStringExpr($exprs[0]);
        for ($i = 1; $i < $numExprs; $i++) {
            $lastConcat = new \ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat($lastConcat, $this->normalizeStringExpr($exprs[$i]));
        }
        return $lastConcat;
    }
    /**
     * @param string|Expr $expr
     * @return Expr
     */
    private function normalizeStringExpr($expr) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if ($expr instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            return $expr;
        }
        if (\is_string($expr)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($expr);
        }
        throw new \LogicException('Expected string or Expr');
    }
}
