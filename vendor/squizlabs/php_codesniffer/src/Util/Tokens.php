<?php

/**
 * Stores weightings and groupings of tokens.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Util;

\define('ECSPrefix20220607\\T_NONE', 'PHPCS_T_NONE');
\define('ECSPrefix20220607\\T_OPEN_CURLY_BRACKET', 'PHPCS_T_OPEN_CURLY_BRACKET');
\define('ECSPrefix20220607\\T_CLOSE_CURLY_BRACKET', 'PHPCS_T_CLOSE_CURLY_BRACKET');
\define('ECSPrefix20220607\\T_OPEN_SQUARE_BRACKET', 'PHPCS_T_OPEN_SQUARE_BRACKET');
\define('ECSPrefix20220607\\T_CLOSE_SQUARE_BRACKET', 'PHPCS_T_CLOSE_SQUARE_BRACKET');
\define('ECSPrefix20220607\\T_OPEN_PARENTHESIS', 'PHPCS_T_OPEN_PARENTHESIS');
\define('ECSPrefix20220607\\T_CLOSE_PARENTHESIS', 'PHPCS_T_CLOSE_PARENTHESIS');
\define('ECSPrefix20220607\\T_COLON', 'PHPCS_T_COLON');
\define('ECSPrefix20220607\\T_NULLABLE', 'PHPCS_T_NULLABLE');
\define('ECSPrefix20220607\\T_STRING_CONCAT', 'PHPCS_T_STRING_CONCAT');
\define('ECSPrefix20220607\\T_INLINE_THEN', 'PHPCS_T_INLINE_THEN');
\define('ECSPrefix20220607\\T_INLINE_ELSE', 'PHPCS_T_INLINE_ELSE');
\define('ECSPrefix20220607\\T_NULL', 'PHPCS_T_NULL');
\define('ECSPrefix20220607\\T_FALSE', 'PHPCS_T_FALSE');
\define('ECSPrefix20220607\\T_TRUE', 'PHPCS_T_TRUE');
\define('ECSPrefix20220607\\T_SEMICOLON', 'PHPCS_T_SEMICOLON');
\define('ECSPrefix20220607\\T_EQUAL', 'PHPCS_T_EQUAL');
\define('ECSPrefix20220607\\T_MULTIPLY', 'PHPCS_T_MULTIPLY');
\define('ECSPrefix20220607\\T_DIVIDE', 'PHPCS_T_DIVIDE');
\define('ECSPrefix20220607\\T_PLUS', 'PHPCS_T_PLUS');
\define('ECSPrefix20220607\\T_MINUS', 'PHPCS_T_MINUS');
\define('ECSPrefix20220607\\T_MODULUS', 'PHPCS_T_MODULUS');
\define('ECSPrefix20220607\\T_BITWISE_AND', 'PHPCS_T_BITWISE_AND');
\define('ECSPrefix20220607\\T_BITWISE_OR', 'PHPCS_T_BITWISE_OR');
\define('ECSPrefix20220607\\T_BITWISE_XOR', 'PHPCS_T_BITWISE_XOR');
\define('ECSPrefix20220607\\T_BITWISE_NOT', 'PHPCS_T_BITWISE_NOT');
\define('ECSPrefix20220607\\T_ARRAY_HINT', 'PHPCS_T_ARRAY_HINT');
\define('ECSPrefix20220607\\T_GREATER_THAN', 'PHPCS_T_GREATER_THAN');
\define('ECSPrefix20220607\\T_LESS_THAN', 'PHPCS_T_LESS_THAN');
\define('ECSPrefix20220607\\T_BOOLEAN_NOT', 'PHPCS_T_BOOLEAN_NOT');
\define('ECSPrefix20220607\\T_SELF', 'PHPCS_T_SELF');
\define('ECSPrefix20220607\\T_PARENT', 'PHPCS_T_PARENT');
\define('ECSPrefix20220607\\T_DOUBLE_QUOTED_STRING', 'PHPCS_T_DOUBLE_QUOTED_STRING');
\define('ECSPrefix20220607\\T_COMMA', 'PHPCS_T_COMMA');
\define('ECSPrefix20220607\\T_HEREDOC', 'PHPCS_T_HEREDOC');
\define('ECSPrefix20220607\\T_PROTOTYPE', 'PHPCS_T_PROTOTYPE');
\define('ECSPrefix20220607\\T_THIS', 'PHPCS_T_THIS');
\define('ECSPrefix20220607\\T_REGULAR_EXPRESSION', 'PHPCS_T_REGULAR_EXPRESSION');
\define('ECSPrefix20220607\\T_PROPERTY', 'PHPCS_T_PROPERTY');
\define('ECSPrefix20220607\\T_LABEL', 'PHPCS_T_LABEL');
\define('ECSPrefix20220607\\T_OBJECT', 'PHPCS_T_OBJECT');
\define('ECSPrefix20220607\\T_CLOSE_OBJECT', 'PHPCS_T_CLOSE_OBJECT');
\define('ECSPrefix20220607\\T_COLOUR', 'PHPCS_T_COLOUR');
\define('ECSPrefix20220607\\T_HASH', 'PHPCS_T_HASH');
\define('ECSPrefix20220607\\T_URL', 'PHPCS_T_URL');
\define('ECSPrefix20220607\\T_STYLE', 'PHPCS_T_STYLE');
\define('ECSPrefix20220607\\T_ASPERAND', 'PHPCS_T_ASPERAND');
\define('ECSPrefix20220607\\T_DOLLAR', 'PHPCS_T_DOLLAR');
\define('ECSPrefix20220607\\T_TYPEOF', 'PHPCS_T_TYPEOF');
\define('ECSPrefix20220607\\T_CLOSURE', 'PHPCS_T_CLOSURE');
\define('ECSPrefix20220607\\T_ANON_CLASS', 'PHPCS_T_ANON_CLASS');
\define('ECSPrefix20220607\\T_BACKTICK', 'PHPCS_T_BACKTICK');
\define('ECSPrefix20220607\\T_START_NOWDOC', 'PHPCS_T_START_NOWDOC');
\define('ECSPrefix20220607\\T_NOWDOC', 'PHPCS_T_NOWDOC');
\define('ECSPrefix20220607\\T_END_NOWDOC', 'PHPCS_T_END_NOWDOC');
\define('ECSPrefix20220607\\T_OPEN_SHORT_ARRAY', 'PHPCS_T_OPEN_SHORT_ARRAY');
\define('ECSPrefix20220607\\T_CLOSE_SHORT_ARRAY', 'PHPCS_T_CLOSE_SHORT_ARRAY');
\define('ECSPrefix20220607\\T_GOTO_LABEL', 'PHPCS_T_GOTO_LABEL');
\define('ECSPrefix20220607\\T_BINARY_CAST', 'PHPCS_T_BINARY_CAST');
\define('ECSPrefix20220607\\T_EMBEDDED_PHP', 'PHPCS_T_EMBEDDED_PHP');
\define('ECSPrefix20220607\\T_RETURN_TYPE', 'PHPCS_T_RETURN_TYPE');
\define('ECSPrefix20220607\\T_OPEN_USE_GROUP', 'PHPCS_T_OPEN_USE_GROUP');
\define('ECSPrefix20220607\\T_CLOSE_USE_GROUP', 'PHPCS_T_CLOSE_USE_GROUP');
\define('ECSPrefix20220607\\T_ZSR', 'PHPCS_T_ZSR');
\define('ECSPrefix20220607\\T_ZSR_EQUAL', 'PHPCS_T_ZSR_EQUAL');
\define('ECSPrefix20220607\\T_FN_ARROW', 'PHPCS_T_FN_ARROW');
\define('ECSPrefix20220607\\T_TYPE_UNION', 'PHPCS_T_TYPE_UNION');
\define('ECSPrefix20220607\\T_PARAM_NAME', 'PHPCS_T_PARAM_NAME');
\define('ECSPrefix20220607\\T_MATCH_ARROW', 'PHPCS_T_MATCH_ARROW');
\define('ECSPrefix20220607\\T_MATCH_DEFAULT', 'PHPCS_T_MATCH_DEFAULT');
\define('ECSPrefix20220607\\T_ATTRIBUTE_END', 'PHPCS_T_ATTRIBUTE_END');
// Some PHP 5.5 tokens, replicated for lower versions.
if (\defined('T_FINALLY') === \false) {
    \define('T_FINALLY', 'PHPCS_T_FINALLY');
}
if (\defined('T_YIELD') === \false) {
    \define('T_YIELD', 'PHPCS_T_YIELD');
}
// Some PHP 5.6 tokens, replicated for lower versions.
if (\defined('T_ELLIPSIS') === \false) {
    \define('T_ELLIPSIS', 'PHPCS_T_ELLIPSIS');
}
if (\defined('T_POW') === \false) {
    \define('T_POW', 'PHPCS_T_POW');
}
if (\defined('T_POW_EQUAL') === \false) {
    \define('T_POW_EQUAL', 'PHPCS_T_POW_EQUAL');
}
// Some PHP 7 tokens, replicated for lower versions.
if (\defined('T_SPACESHIP') === \false) {
    \define('T_SPACESHIP', 'PHPCS_T_SPACESHIP');
}
if (\defined('T_COALESCE') === \false) {
    \define('T_COALESCE', 'PHPCS_T_COALESCE');
}
if (\defined('T_COALESCE_EQUAL') === \false) {
    \define('T_COALESCE_EQUAL', 'PHPCS_T_COALESCE_EQUAL');
}
if (\defined('T_YIELD_FROM') === \false) {
    \define('T_YIELD_FROM', 'PHPCS_T_YIELD_FROM');
}
// Some PHP 7.4 tokens, replicated for lower versions.
if (\defined('T_BAD_CHARACTER') === \false) {
    \define('T_BAD_CHARACTER', 'PHPCS_T_BAD_CHARACTER');
}
if (\defined('T_FN') === \false) {
    \define('T_FN', 'PHPCS_T_FN');
}
// Some PHP 8.0 tokens, replicated for lower versions.
if (\defined('T_NULLSAFE_OBJECT_OPERATOR') === \false) {
    \define('T_NULLSAFE_OBJECT_OPERATOR', 'PHPCS_T_NULLSAFE_OBJECT_OPERATOR');
}
if (\defined('T_NAME_QUALIFIED') === \false) {
    \define('T_NAME_QUALIFIED', 'PHPCS_T_NAME_QUALIFIED');
}
if (\defined('T_NAME_FULLY_QUALIFIED') === \false) {
    \define('T_NAME_FULLY_QUALIFIED', 'PHPCS_T_NAME_FULLY_QUALIFIED');
}
if (\defined('T_NAME_RELATIVE') === \false) {
    \define('T_NAME_RELATIVE', 'PHPCS_T_NAME_RELATIVE');
}
if (\defined('T_MATCH') === \false) {
    \define('T_MATCH', 'PHPCS_T_MATCH');
}
if (\defined('T_ATTRIBUTE') === \false) {
    \define('T_ATTRIBUTE', 'PHPCS_T_ATTRIBUTE');
}
// Some PHP 8.1 tokens, replicated for lower versions.
if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG') === \false) {
    \define('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG', 'PHPCS_T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG');
}
if (\defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') === \false) {
    \define('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', 'PHPCS_T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG');
}
// Tokens used for parsing doc blocks.
\define('ECSPrefix20220607\\T_DOC_COMMENT_STAR', 'PHPCS_T_DOC_COMMENT_STAR');
\define('ECSPrefix20220607\\T_DOC_COMMENT_WHITESPACE', 'PHPCS_T_DOC_COMMENT_WHITESPACE');
\define('ECSPrefix20220607\\T_DOC_COMMENT_TAG', 'PHPCS_T_DOC_COMMENT_TAG');
\define('ECSPrefix20220607\\T_DOC_COMMENT_OPEN_TAG', 'PHPCS_T_DOC_COMMENT_OPEN_TAG');
\define('ECSPrefix20220607\\T_DOC_COMMENT_CLOSE_TAG', 'PHPCS_T_DOC_COMMENT_CLOSE_TAG');
\define('ECSPrefix20220607\\T_DOC_COMMENT_STRING', 'PHPCS_T_DOC_COMMENT_STRING');
// Tokens used for PHPCS instruction comments.
\define('ECSPrefix20220607\\T_PHPCS_ENABLE', 'PHPCS_T_PHPCS_ENABLE');
\define('ECSPrefix20220607\\T_PHPCS_DISABLE', 'PHPCS_T_PHPCS_DISABLE');
\define('ECSPrefix20220607\\T_PHPCS_SET', 'PHPCS_T_PHPCS_SET');
\define('ECSPrefix20220607\\T_PHPCS_IGNORE', 'PHPCS_T_PHPCS_IGNORE');
\define('ECSPrefix20220607\\T_PHPCS_IGNORE_FILE', 'PHPCS_T_PHPCS_IGNORE_FILE');
final class Tokens
{
    /**
     * The token weightings.
     *
     * @var array<int|string, int>
     */
    public static $weightings = [
        \T_CLASS => 1000,
        \T_INTERFACE => 1000,
        \T_TRAIT => 1000,
        \T_NAMESPACE => 1000,
        \T_FUNCTION => 100,
        T_CLOSURE => 100,
        /*
         * Conditions.
         */
        \T_WHILE => 50,
        \T_FOR => 50,
        \T_FOREACH => 50,
        \T_IF => 50,
        \T_ELSE => 50,
        \T_ELSEIF => 50,
        \T_DO => 50,
        \T_TRY => 50,
        \T_CATCH => 50,
        \T_FINALLY => 50,
        \T_SWITCH => 50,
        \T_MATCH => 50,
        T_SELF => 25,
        T_PARENT => 25,
        /*
         * Operators and arithmetic.
         */
        T_BITWISE_AND => 8,
        T_BITWISE_OR => 8,
        T_BITWISE_XOR => 8,
        T_MULTIPLY => 5,
        T_DIVIDE => 5,
        T_PLUS => 5,
        T_MINUS => 5,
        T_MODULUS => 5,
        \T_POW => 5,
        \T_SPACESHIP => 5,
        \T_COALESCE => 5,
        \T_COALESCE_EQUAL => 5,
        \T_SL => 5,
        \T_SR => 5,
        \T_SL_EQUAL => 5,
        \T_SR_EQUAL => 5,
        T_EQUAL => 5,
        \T_AND_EQUAL => 5,
        \T_CONCAT_EQUAL => 5,
        \T_DIV_EQUAL => 5,
        \T_MINUS_EQUAL => 5,
        \T_MOD_EQUAL => 5,
        \T_MUL_EQUAL => 5,
        \T_OR_EQUAL => 5,
        \T_PLUS_EQUAL => 5,
        \T_XOR_EQUAL => 5,
        \T_BOOLEAN_AND => 5,
        \T_BOOLEAN_OR => 5,
        /*
         * Equality.
         */
        \T_IS_EQUAL => 5,
        \T_IS_NOT_EQUAL => 5,
        \T_IS_IDENTICAL => 5,
        \T_IS_NOT_IDENTICAL => 5,
        \T_IS_SMALLER_OR_EQUAL => 5,
        \T_IS_GREATER_OR_EQUAL => 5,
    ];
    /**
     * Tokens that represent assignments.
     *
     * @var array<int|string, int|string>
     */
    public static $assignmentTokens = [T_EQUAL => T_EQUAL, \T_AND_EQUAL => \T_AND_EQUAL, \T_OR_EQUAL => \T_OR_EQUAL, \T_CONCAT_EQUAL => \T_CONCAT_EQUAL, \T_DIV_EQUAL => \T_DIV_EQUAL, \T_MINUS_EQUAL => \T_MINUS_EQUAL, \T_POW_EQUAL => \T_POW_EQUAL, \T_MOD_EQUAL => \T_MOD_EQUAL, \T_MUL_EQUAL => \T_MUL_EQUAL, \T_PLUS_EQUAL => \T_PLUS_EQUAL, \T_XOR_EQUAL => \T_XOR_EQUAL, \T_DOUBLE_ARROW => \T_DOUBLE_ARROW, \T_SL_EQUAL => \T_SL_EQUAL, \T_SR_EQUAL => \T_SR_EQUAL, \T_COALESCE_EQUAL => \T_COALESCE_EQUAL, T_ZSR_EQUAL => T_ZSR_EQUAL];
    /**
     * Tokens that represent equality comparisons.
     *
     * @var array<int|string, int|string>
     */
    public static $equalityTokens = [\T_IS_EQUAL => \T_IS_EQUAL, \T_IS_NOT_EQUAL => \T_IS_NOT_EQUAL, \T_IS_IDENTICAL => \T_IS_IDENTICAL, \T_IS_NOT_IDENTICAL => \T_IS_NOT_IDENTICAL, \T_IS_SMALLER_OR_EQUAL => \T_IS_SMALLER_OR_EQUAL, \T_IS_GREATER_OR_EQUAL => \T_IS_GREATER_OR_EQUAL];
    /**
     * Tokens that represent comparison operator.
     *
     * @var array<int|string, int|string>
     */
    public static $comparisonTokens = [\T_IS_EQUAL => \T_IS_EQUAL, \T_IS_IDENTICAL => \T_IS_IDENTICAL, \T_IS_NOT_EQUAL => \T_IS_NOT_EQUAL, \T_IS_NOT_IDENTICAL => \T_IS_NOT_IDENTICAL, T_LESS_THAN => T_LESS_THAN, T_GREATER_THAN => T_GREATER_THAN, \T_IS_SMALLER_OR_EQUAL => \T_IS_SMALLER_OR_EQUAL, \T_IS_GREATER_OR_EQUAL => \T_IS_GREATER_OR_EQUAL, \T_SPACESHIP => \T_SPACESHIP, \T_COALESCE => \T_COALESCE];
    /**
     * Tokens that represent arithmetic operators.
     *
     * @var array<int|string, int|string>
     */
    public static $arithmeticTokens = [T_PLUS => T_PLUS, T_MINUS => T_MINUS, T_MULTIPLY => T_MULTIPLY, T_DIVIDE => T_DIVIDE, T_MODULUS => T_MODULUS, \T_POW => \T_POW];
    /**
     * Tokens that perform operations.
     *
     * @var array<int|string, int|string>
     */
    public static $operators = [T_MINUS => T_MINUS, T_PLUS => T_PLUS, T_MULTIPLY => T_MULTIPLY, T_DIVIDE => T_DIVIDE, T_MODULUS => T_MODULUS, \T_POW => \T_POW, \T_SPACESHIP => \T_SPACESHIP, \T_COALESCE => \T_COALESCE, T_BITWISE_AND => T_BITWISE_AND, T_BITWISE_OR => T_BITWISE_OR, T_BITWISE_XOR => T_BITWISE_XOR, \T_SL => \T_SL, \T_SR => \T_SR];
    /**
     * Tokens that perform boolean operations.
     *
     * @var array<int|string, int|string>
     */
    public static $booleanOperators = [\T_BOOLEAN_AND => \T_BOOLEAN_AND, \T_BOOLEAN_OR => \T_BOOLEAN_OR, \T_LOGICAL_AND => \T_LOGICAL_AND, \T_LOGICAL_OR => \T_LOGICAL_OR, \T_LOGICAL_XOR => \T_LOGICAL_XOR];
    /**
     * Tokens that represent casting.
     *
     * @var array<int|string, int|string>
     */
    public static $castTokens = [\T_INT_CAST => \T_INT_CAST, \T_STRING_CAST => \T_STRING_CAST, \T_DOUBLE_CAST => \T_DOUBLE_CAST, \T_ARRAY_CAST => \T_ARRAY_CAST, \T_BOOL_CAST => \T_BOOL_CAST, \T_OBJECT_CAST => \T_OBJECT_CAST, \T_UNSET_CAST => \T_UNSET_CAST, T_BINARY_CAST => T_BINARY_CAST];
    /**
     * Token types that open parenthesis.
     *
     * @var array<int|string, int|string>
     */
    public static $parenthesisOpeners = [\T_ARRAY => \T_ARRAY, \T_LIST => \T_LIST, \T_FUNCTION => \T_FUNCTION, T_CLOSURE => T_CLOSURE, T_ANON_CLASS => T_ANON_CLASS, \T_WHILE => \T_WHILE, \T_FOR => \T_FOR, \T_FOREACH => \T_FOREACH, \T_SWITCH => \T_SWITCH, \T_IF => \T_IF, \T_ELSEIF => \T_ELSEIF, \T_CATCH => \T_CATCH, \T_DECLARE => \T_DECLARE, \T_MATCH => \T_MATCH];
    /**
     * Tokens that are allowed to open scopes.
     *
     * @var array<int|string, int|string>
     */
    public static $scopeOpeners = [\T_CLASS => \T_CLASS, T_ANON_CLASS => T_ANON_CLASS, \T_INTERFACE => \T_INTERFACE, \T_TRAIT => \T_TRAIT, \T_NAMESPACE => \T_NAMESPACE, \T_FUNCTION => \T_FUNCTION, T_CLOSURE => T_CLOSURE, \T_IF => \T_IF, \T_SWITCH => \T_SWITCH, \T_CASE => \T_CASE, \T_DECLARE => \T_DECLARE, \T_DEFAULT => \T_DEFAULT, \T_WHILE => \T_WHILE, \T_ELSE => \T_ELSE, \T_ELSEIF => \T_ELSEIF, \T_FOR => \T_FOR, \T_FOREACH => \T_FOREACH, \T_DO => \T_DO, \T_TRY => \T_TRY, \T_CATCH => \T_CATCH, \T_FINALLY => \T_FINALLY, T_PROPERTY => T_PROPERTY, T_OBJECT => T_OBJECT, \T_USE => \T_USE, \T_MATCH => \T_MATCH];
    /**
     * Tokens that represent scope modifiers.
     *
     * @var array<int|string, int|string>
     */
    public static $scopeModifiers = [\T_PRIVATE => \T_PRIVATE, \T_PUBLIC => \T_PUBLIC, \T_PROTECTED => \T_PROTECTED];
    /**
     * Tokens that can prefix a method name
     *
     * @var array<int|string, int|string>
     */
    public static $methodPrefixes = [\T_PRIVATE => \T_PRIVATE, \T_PUBLIC => \T_PUBLIC, \T_PROTECTED => \T_PROTECTED, \T_ABSTRACT => \T_ABSTRACT, \T_STATIC => \T_STATIC, \T_FINAL => \T_FINAL];
    /**
     * Tokens that open code blocks.
     *
     * @var array<int|string, int|string>
     */
    public static $blockOpeners = [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET, T_OPEN_SQUARE_BRACKET => T_OPEN_SQUARE_BRACKET, T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS, T_OBJECT => T_OBJECT];
    /**
     * Tokens that don't represent code.
     *
     * @var array<int|string, int|string>
     */
    public static $emptyTokens = [\T_WHITESPACE => \T_WHITESPACE, \T_COMMENT => \T_COMMENT, \T_DOC_COMMENT => \T_DOC_COMMENT, T_DOC_COMMENT_STAR => T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE => T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_TAG => T_DOC_COMMENT_TAG, T_DOC_COMMENT_OPEN_TAG => T_DOC_COMMENT_OPEN_TAG, T_DOC_COMMENT_CLOSE_TAG => T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STRING => T_DOC_COMMENT_STRING, T_PHPCS_ENABLE => T_PHPCS_ENABLE, T_PHPCS_DISABLE => T_PHPCS_DISABLE, T_PHPCS_SET => T_PHPCS_SET, T_PHPCS_IGNORE => T_PHPCS_IGNORE, T_PHPCS_IGNORE_FILE => T_PHPCS_IGNORE_FILE];
    /**
     * Tokens that are comments.
     *
     * @var array<int|string, int|string>
     */
    public static $commentTokens = [\T_COMMENT => \T_COMMENT, \T_DOC_COMMENT => \T_DOC_COMMENT, T_DOC_COMMENT_STAR => T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE => T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_TAG => T_DOC_COMMENT_TAG, T_DOC_COMMENT_OPEN_TAG => T_DOC_COMMENT_OPEN_TAG, T_DOC_COMMENT_CLOSE_TAG => T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STRING => T_DOC_COMMENT_STRING, T_PHPCS_ENABLE => T_PHPCS_ENABLE, T_PHPCS_DISABLE => T_PHPCS_DISABLE, T_PHPCS_SET => T_PHPCS_SET, T_PHPCS_IGNORE => T_PHPCS_IGNORE, T_PHPCS_IGNORE_FILE => T_PHPCS_IGNORE_FILE];
    /**
     * Tokens that are comments containing PHPCS instructions.
     *
     * @var array<int|string, int|string>
     */
    public static $phpcsCommentTokens = [T_PHPCS_ENABLE => T_PHPCS_ENABLE, T_PHPCS_DISABLE => T_PHPCS_DISABLE, T_PHPCS_SET => T_PHPCS_SET, T_PHPCS_IGNORE => T_PHPCS_IGNORE, T_PHPCS_IGNORE_FILE => T_PHPCS_IGNORE_FILE];
    /**
     * Tokens that represent strings.
     *
     * Note that T_STRINGS are NOT represented in this list.
     *
     * @var array<int|string, int|string>
     */
    public static $stringTokens = [\T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING => T_DOUBLE_QUOTED_STRING];
    /**
     * Tokens that represent text strings.
     *
     * @var array<int|string, int|string>
     */
    public static $textStringTokens = [\T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING => T_DOUBLE_QUOTED_STRING, \T_INLINE_HTML => \T_INLINE_HTML, T_HEREDOC => T_HEREDOC, T_NOWDOC => T_NOWDOC];
    /**
     * Tokens that represent brackets and parenthesis.
     *
     * @var array<int|string, int|string>
     */
    public static $bracketTokens = [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET, T_OPEN_SQUARE_BRACKET => T_OPEN_SQUARE_BRACKET, T_CLOSE_SQUARE_BRACKET => T_CLOSE_SQUARE_BRACKET, T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS];
    /**
     * Tokens that include files.
     *
     * @var array<int|string, int|string>
     */
    public static $includeTokens = [\T_REQUIRE_ONCE => \T_REQUIRE_ONCE, \T_REQUIRE => \T_REQUIRE, \T_INCLUDE_ONCE => \T_INCLUDE_ONCE, \T_INCLUDE => \T_INCLUDE];
    /**
     * Tokens that make up a heredoc string.
     *
     * @var array<int|string, int|string>
     */
    public static $heredocTokens = [\T_START_HEREDOC => \T_START_HEREDOC, \T_END_HEREDOC => \T_END_HEREDOC, T_HEREDOC => T_HEREDOC, T_START_NOWDOC => T_START_NOWDOC, T_END_NOWDOC => T_END_NOWDOC, T_NOWDOC => T_NOWDOC];
    /**
     * Tokens that represent the names of called functions.
     *
     * Mostly, these are just strings. But PHP tokenizes some language
     * constructs and functions using their own tokens.
     *
     * @var array<int|string, int|string>
     */
    public static $functionNameTokens = [\T_STRING => \T_STRING, \T_EVAL => \T_EVAL, \T_EXIT => \T_EXIT, \T_INCLUDE => \T_INCLUDE, \T_INCLUDE_ONCE => \T_INCLUDE_ONCE, \T_REQUIRE => \T_REQUIRE, \T_REQUIRE_ONCE => \T_REQUIRE_ONCE, \T_ISSET => \T_ISSET, \T_UNSET => \T_UNSET, \T_EMPTY => \T_EMPTY, T_SELF => T_SELF, \T_STATIC => \T_STATIC];
    /**
     * Tokens that open class and object scopes.
     *
     * @var array<int|string, int|string>
     */
    public static $ooScopeTokens = [\T_CLASS => \T_CLASS, T_ANON_CLASS => T_ANON_CLASS, \T_INTERFACE => \T_INTERFACE, \T_TRAIT => \T_TRAIT];
    /**
     * Tokens representing PHP magic constants.
     *
     * @var array <int|string> => <int|string>
     *
     * @link https://www.php.net/language.constants.predefined PHP Manual on magic constants
     */
    public static $magicConstants = [\T_CLASS_C => \T_CLASS_C, \T_DIR => \T_DIR, \T_FILE => \T_FILE, \T_FUNC_C => \T_FUNC_C, \T_LINE => \T_LINE, \T_METHOD_C => \T_METHOD_C, \T_NS_C => \T_NS_C, \T_TRAIT_C => \T_TRAIT_C];
    /**
     * Given a token, returns the name of the token.
     *
     * If passed an integer, the token name is sourced from PHP's token_name()
     * function. If passed a string, it is assumed to be a PHPCS-supplied token
     * that begins with PHPCS_T_, so the name is sourced from the token value itself.
     *
     * @param int|string $token The token to get the name for.
     *
     * @return string
     */
    public static function tokenName($token)
    {
        if (\is_string($token) === \false) {
            // PHP-supplied token name.
            return \token_name($token);
        }
        return \substr($token, 6);
    }
    //end tokenName()
    /**
     * Returns the highest weighted token type.
     *
     * Tokens are weighted by their approximate frequency of appearance in code
     * - the less frequently they appear in the code, the higher the weighting.
     * For example T_CLASS tokens appear very infrequently in a file, and
     * therefore have a high weighting.
     *
     * Returns false if there are no weightings for any of the specified tokens.
     *
     * @param array<int|string> $tokens The token types to get the highest weighted
     *                                  type for.
     *
     * @return int|false The highest weighted token.
     */
    public static function getHighestWeightedToken(array $tokens)
    {
        $highest = -1;
        $highestType = \false;
        $weights = self::$weightings;
        foreach ($tokens as $token) {
            if (isset($weights[$token]) === \true) {
                $weight = $weights[$token];
            } else {
                $weight = 0;
            }
            if ($weight > $highest) {
                $highest = $weight;
                $highestType = $token;
            }
        }
        return $highestType;
    }
    //end getHighestWeightedToken()
}
//end class
