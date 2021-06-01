<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser;

use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\Identifier;
use ConfigTransformer20210601\PhpParser\Node\Name;
use ConfigTransformer20210601\PhpParser\Node\NullableType;
use ConfigTransformer20210601\PhpParser\Node\Scalar;
use ConfigTransformer20210601\PhpParser\Node\Stmt;
use ConfigTransformer20210601\PhpParser\Node\UnionType;
/**
 * This class defines helpers used in the implementation of builders. Don't use it directly.
 *
 * @internal
 */
final class BuilderHelpers
{
    /**
     * Normalizes a node: Converts builder objects to nodes.
     *
     * @param Node|Builder $node The node to normalize
     *
     * @return Node The normalized node
     */
    public static function normalizeNode($node) : \ConfigTransformer20210601\PhpParser\Node
    {
        if ($node instanceof \ConfigTransformer20210601\PhpParser\Builder) {
            return $node->getNode();
        } elseif ($node instanceof \ConfigTransformer20210601\PhpParser\Node) {
            return $node;
        }
        throw new \LogicException('Expected node or builder object');
    }
    /**
     * Normalizes a node to a statement.
     *
     * Expressions are wrapped in a Stmt\Expression node.
     *
     * @param Node|Builder $node The node to normalize
     *
     * @return Stmt The normalized statement node
     */
    public static function normalizeStmt($node) : \ConfigTransformer20210601\PhpParser\Node\Stmt
    {
        $node = self::normalizeNode($node);
        if ($node instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt) {
            return $node;
        }
        if ($node instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($node);
        }
        throw new \LogicException('Expected statement or expression node');
    }
    /**
     * Normalizes strings to Identifier.
     *
     * @param string|Identifier $name The identifier to normalize
     *
     * @return Identifier The normalized identifier
     */
    public static function normalizeIdentifier($name) : \ConfigTransformer20210601\PhpParser\Node\Identifier
    {
        if ($name instanceof \ConfigTransformer20210601\PhpParser\Node\Identifier) {
            return $name;
        }
        if (\is_string($name)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Identifier($name);
        }
        throw new \LogicException('ConfigTransformer20210601\\Expected string or instance of Node\\Identifier');
    }
    /**
     * Normalizes strings to Identifier, also allowing expressions.
     *
     * @param string|Identifier|Expr $name The identifier to normalize
     *
     * @return Identifier|Expr The normalized identifier or expression
     */
    public static function normalizeIdentifierOrExpr($name)
    {
        if ($name instanceof \ConfigTransformer20210601\PhpParser\Node\Identifier || $name instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            return $name;
        }
        if (\is_string($name)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Identifier($name);
        }
        throw new \LogicException('ConfigTransformer20210601\\Expected string or instance of Node\\Identifier or Node\\Expr');
    }
    /**
     * Normalizes a name: Converts string names to Name nodes.
     *
     * @param Name|string $name The name to normalize
     *
     * @return Name The normalized name
     */
    public static function normalizeName($name) : \ConfigTransformer20210601\PhpParser\Node\Name
    {
        return self::normalizeNameCommon($name, \false);
    }
    /**
     * Normalizes a name: Converts string names to Name nodes, while also allowing expressions.
     *
     * @param Expr|Name|string $name The name to normalize
     *
     * @return Name|Expr The normalized name or expression
     */
    public static function normalizeNameOrExpr($name)
    {
        return self::normalizeNameCommon($name, \true);
    }
    /**
     * Normalizes a name: Converts string names to Name nodes, optionally allowing expressions.
     *
     * @param Expr|Name|string $name      The name to normalize
     * @param bool             $allowExpr Whether to also allow expressions
     *
     * @return Name|Expr The normalized name, or expression (if allowed)
     */
    private static function normalizeNameCommon($name, bool $allowExpr)
    {
        if ($name instanceof \ConfigTransformer20210601\PhpParser\Node\Name) {
            return $name;
        } elseif (\is_string($name)) {
            if (!$name) {
                throw new \LogicException('Name cannot be empty');
            }
            if ($name[0] === '\\') {
                return new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified(\substr($name, 1));
            } elseif (0 === \strpos($name, 'namespace\\')) {
                return new \ConfigTransformer20210601\PhpParser\Node\Name\Relative(\substr($name, \strlen('namespace\\')));
            } else {
                return new \ConfigTransformer20210601\PhpParser\Node\Name($name);
            }
        }
        if ($allowExpr) {
            if ($name instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
                return $name;
            }
            throw new \LogicException('ConfigTransformer20210601\\Name must be a string or an instance of Node\\Name or Node\\Expr');
        } else {
            throw new \LogicException('ConfigTransformer20210601\\Name must be a string or an instance of Node\\Name');
        }
    }
    /**
     * Normalizes a type: Converts plain-text type names into proper AST representation.
     *
     * In particular, builtin types become Identifiers, custom types become Names and nullables
     * are wrapped in NullableType nodes.
     *
     * @param string|Name|Identifier|NullableType|UnionType $type The type to normalize
     *
     * @return Name|Identifier|NullableType|UnionType The normalized type
     */
    public static function normalizeType($type)
    {
        if (!\is_string($type)) {
            if (!$type instanceof \ConfigTransformer20210601\PhpParser\Node\Name && !$type instanceof \ConfigTransformer20210601\PhpParser\Node\Identifier && !$type instanceof \ConfigTransformer20210601\PhpParser\Node\NullableType && !$type instanceof \ConfigTransformer20210601\PhpParser\Node\UnionType) {
                throw new \LogicException('Type must be a string, or an instance of Name, Identifier, NullableType or UnionType');
            }
            return $type;
        }
        $nullable = \false;
        if (\strlen($type) > 0 && $type[0] === '?') {
            $nullable = \true;
            $type = \substr($type, 1);
        }
        $builtinTypes = ['array', 'callable', 'string', 'int', 'float', 'bool', 'iterable', 'void', 'object', 'mixed'];
        $lowerType = \strtolower($type);
        if (\in_array($lowerType, $builtinTypes)) {
            $type = new \ConfigTransformer20210601\PhpParser\Node\Identifier($lowerType);
        } else {
            $type = self::normalizeName($type);
        }
        if ($nullable && (string) $type === 'void') {
            throw new \LogicException('void type cannot be nullable');
        }
        if ($nullable && (string) $type === 'mixed') {
            throw new \LogicException('mixed type cannot be nullable');
        }
        return $nullable ? new \ConfigTransformer20210601\PhpParser\Node\NullableType($type) : $type;
    }
    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param Node\Expr|bool|null|int|float|string|array $value The value to normalize
     *
     * @return Expr The normalized value
     */
    public static function normalizeValue($value) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if ($value instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            return $value;
        } elseif (\is_null($value)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch(new \ConfigTransformer20210601\PhpParser\Node\Name('null'));
        } elseif (\is_bool($value)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch(new \ConfigTransformer20210601\PhpParser\Node\Name($value ? 'true' : 'false'));
        } elseif (\is_int($value)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\LNumber($value);
        } elseif (\is_float($value)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\DNumber($value);
        } elseif (\is_string($value)) {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($value);
        } elseif (\is_array($value)) {
            $items = [];
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem(self::normalizeValue($itemValue));
                } else {
                    $lastKey = null;
                    $items[] = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem(self::normalizeValue($itemValue), self::normalizeValue($itemKey));
                }
            }
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_($items);
        } else {
            throw new \LogicException('Invalid value');
        }
    }
    /**
     * Normalizes a doc comment: Converts plain strings to PhpParser\Comment\Doc.
     *
     * @param Comment\Doc|string $docComment The doc comment to normalize
     *
     * @return Comment\Doc The normalized doc comment
     */
    public static function normalizeDocComment($docComment) : \ConfigTransformer20210601\PhpParser\Comment\Doc
    {
        if ($docComment instanceof \ConfigTransformer20210601\PhpParser\Comment\Doc) {
            return $docComment;
        } elseif (\is_string($docComment)) {
            return new \ConfigTransformer20210601\PhpParser\Comment\Doc($docComment);
        } else {
            throw new \LogicException('ConfigTransformer20210601\\Doc comment must be a string or an instance of PhpParser\\Comment\\Doc');
        }
    }
    /**
     * Adds a modifier and returns new modifier bitmask.
     *
     * @param int $modifiers Existing modifiers
     * @param int $modifier  Modifier to set
     *
     * @return int New modifiers
     */
    public static function addModifier(int $modifiers, int $modifier) : int
    {
        \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::verifyModifier($modifiers, $modifier);
        return $modifiers | $modifier;
    }
}
