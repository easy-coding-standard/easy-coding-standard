<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Tokenizer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CT
{
    /** @deprecated use T_ARRAY_INDEX_BRACE_CLOSE instead */
    public const T_ARRAY_INDEX_CURLY_BRACE_CLOSE = self::T_ARRAY_INDEX_BRACE_CLOSE;
    public const T_ARRAY_INDEX_BRACE_CLOSE = 10001;
    /** @deprecated use T_ARRAY_INDEX_BRACE_OPEN instead */
    public const T_ARRAY_INDEX_CURLY_BRACE_OPEN = self::T_ARRAY_INDEX_BRACE_OPEN;
    public const T_ARRAY_INDEX_BRACE_OPEN = 10002;
    /** @deprecated use T_ARRAY_BRACKET_CLOSE instead */
    public const T_ARRAY_SQUARE_BRACE_CLOSE = self::T_ARRAY_BRACKET_CLOSE;
    public const T_ARRAY_BRACKET_CLOSE = 10003;
    /** @deprecated use T_ARRAY_BRACKET_OPEN instead */
    public const T_ARRAY_SQUARE_BRACE_OPEN = self::T_ARRAY_BRACKET_OPEN;
    public const T_ARRAY_BRACKET_OPEN = 10004;
    public const T_ARRAY_TYPEHINT = 10005;
    /** @deprecated use T_CLASS_INSTANTIATION_PARENTHESIS_CLOSE instead */
    public const T_BRACE_CLASS_INSTANTIATION_CLOSE = self::T_CLASS_INSTANTIATION_PARENTHESIS_CLOSE;
    public const T_CLASS_INSTANTIATION_PARENTHESIS_CLOSE = 10006;
    /** @deprecated use T_CLASS_INSTANTIATION_PARENTHESIS_OPEN instead */
    public const T_BRACE_CLASS_INSTANTIATION_OPEN = self::T_CLASS_INSTANTIATION_PARENTHESIS_OPEN;
    public const T_CLASS_INSTANTIATION_PARENTHESIS_OPEN = 10007;
    public const T_CLASS_CONSTANT = 10008;
    public const T_CONST_IMPORT = 10009;
    public const T_CURLY_CLOSE = 10010;
    // We explicitly use CURLY part in name to mimic built-in \T_CURLY_OPEN
    /** @deprecated use T_DESTRUCTURING_BRACKET_CLOSE instead */
    public const T_DESTRUCTURING_SQUARE_BRACE_CLOSE = self::T_DESTRUCTURING_BRACKET_CLOSE;
    public const T_DESTRUCTURING_BRACKET_CLOSE = 10011;
    /** @deprecated use T_DESTRUCTURING_BRACKET_OPEN instead */
    public const T_DESTRUCTURING_SQUARE_BRACE_OPEN = self::T_DESTRUCTURING_BRACKET_OPEN;
    public const T_DESTRUCTURING_BRACKET_OPEN = 10012;
    public const T_DOLLAR_CLOSE_CURLY_BRACES = 10013;
    // We explicitly use CURLY part in name to mimic built-in \T_DOLLAR_OPEN_CURLY_BRACES
    public const T_DYNAMIC_PROP_BRACE_CLOSE = 10014;
    public const T_DYNAMIC_PROP_BRACE_OPEN = 10015;
    public const T_DYNAMIC_VAR_BRACE_CLOSE = 10016;
    public const T_DYNAMIC_VAR_BRACE_OPEN = 10017;
    public const T_FUNCTION_IMPORT = 10018;
    public const T_GROUP_IMPORT_BRACE_CLOSE = 10019;
    public const T_GROUP_IMPORT_BRACE_OPEN = 10020;
    public const T_NAMESPACE_OPERATOR = 10021;
    public const T_NULLABLE_TYPE = 10022;
    public const T_RETURN_REF = 10023;
    public const T_TYPE_ALTERNATION = 10024;
    public const T_TYPE_COLON = 10025;
    public const T_USE_LAMBDA = 10026;
    public const T_USE_TRAIT = 10027;
    public const T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC = 10028;
    public const T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED = 10029;
    public const T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE = 10030;
    public const T_ATTRIBUTE_CLOSE = 10031;
    // We explicitly skip BRACKET part in name to mimic built-in \T_ATTRIBUTE
    public const T_NAMED_ARGUMENT_NAME = 10032;
    public const T_NAMED_ARGUMENT_COLON = 10033;
    public const T_FIRST_CLASS_CALLABLE = 10034;
    public const T_TYPE_INTERSECTION = 10035;
    public const T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN = 10036;
    public const T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE = 10037;
    /** @deprecated use T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_OPEN instead */
    public const T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN = self::T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_OPEN;
    public const T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_OPEN = 10038;
    /** @deprecated use T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_CLOSE instead */
    public const T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE = self::T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_CLOSE;
    public const T_DYNAMIC_CLASS_CONSTANT_FETCH_BRACE_CLOSE = 10039;
    public const T_PROPERTY_HOOK_BRACE_OPEN = 10040;
    public const T_PROPERTY_HOOK_BRACE_CLOSE = 10041;
    private function __construct()
    {
    }
    /**
     * Get name for custom token.
     *
     * @param int $value custom token value
     *
     * @return non-empty-string
     */
    public static function getName(int $value): string
    {
        if (!self::has($value)) {
            throw new \InvalidArgumentException(\sprintf('No custom token was found for "%s".', $value));
        }
        $tokens = self::getMapById();
        \assert(isset($tokens[$value]));
        return 'CT::' . $tokens[$value];
    }
    /**
     * Check if given custom token exists.
     *
     * @param int $value custom token value
     */
    public static function has(int $value): bool
    {
        $tokens = self::getMapById();
        return isset($tokens[$value]);
    }
    /**
     * @return array<self::T_*, non-empty-string>
     */
    private static function getMapById(): array
    {
        static $constants;
        if (null === $constants) {
            $reflection = new \ReflectionClass(self::class);
            $constants = array_flip($reflection->getConstants());
        }
        return $constants;
    }
}
