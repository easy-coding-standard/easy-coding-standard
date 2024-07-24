<?php

/**
 * Tests the backfilling of the T_FN token to PHP < 7.4.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Tokenizer\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizer\AbstractTokenizerTestCase;
final class BackfillFnTokenTest extends AbstractTokenizerTestCase
{
    /**
     * Test simple arrow functions.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataSimple
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testSimple($testMarker)
    {
        $token = $this->getTargetToken($testMarker, \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 12);
    }
    //end testSimple()
    /**
     * Data provider.
     *
     * @see testSimple()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataSimple()
    {
        return ['standard' => ['testMarker' => '/* testStandard */'], 'mixed case' => ['testMarker' => '/* testMixedCase */']];
    }
    //end dataSimple()
    /**
     * Test whitespace inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWhitespace()
    {
        $token = $this->getTargetToken('/* testWhitespace */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 6, 13);
    }
    //end testWhitespace()
    /**
     * Test comments inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testComment()
    {
        $token = $this->getTargetToken('/* testComment */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 15);
    }
    //end testComment()
    /**
     * Test heredocs inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testHeredoc()
    {
        $token = $this->getTargetToken('/* testHeredoc */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 9);
    }
    //end testHeredoc()
    /**
     * Test nested arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedOuter()
    {
        $token = $this->getTargetToken('/* testNestedOuter */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 25);
    }
    //end testNestedOuter()
    /**
     * Test nested arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedInner()
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken('/* testNestedInner */', \T_FN);
        $this->backfillHelper($token, \true);
        $expectedScopeOpener = $token + 5;
        $expectedScopeCloser = $token + 16;
        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer is not the semicolon token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer is not the semicolon token');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($token - 4, $tokens[$closer]['scope_opener'], 'Closer scope opener is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer is not the semicolon token');
    }
    //end testNestedInner()
    /**
     * Test nested arrow functions with a shared closer.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedSharedCloser()
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken('/* testNestedSharedCloserOuter */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 20);
        $token = $this->getTargetToken('/* testNestedSharedCloserInner */', \T_FN);
        $this->backfillHelper($token, \true);
        $expectedScopeOpener = $token + 4;
        $expectedScopeCloser = $token + 12;
        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener for "inner" arrow function is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer for "inner" arrow function is not the TRUE token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener for "inner" arrow function is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer for "inner" arrow function is not the semicolon token');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($token - 4, $tokens[$closer]['scope_opener'], 'Closer scope opener for "inner" arrow function is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer for "inner" arrow function is not the TRUE token');
    }
    //end testNestedSharedCloser()
    /**
     * Test arrow functions that call functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFunctionCall()
    {
        $token = $this->getTargetToken('/* testFunctionCall */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 17);
    }
    //end testFunctionCall()
    /**
     * Test arrow functions that are included in chained calls.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testChainedFunctionCall()
    {
        $token = $this->getTargetToken('/* testChainedFunctionCall */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 12, 'bracket');
    }
    //end testChainedFunctionCall()
    /**
     * Test arrow functions that are used as function arguments.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFunctionArgument()
    {
        $token = $this->getTargetToken('/* testFunctionArgument */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 15, 'comma');
    }
    //end testFunctionArgument()
    /**
     * Test arrow functions that use closures.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testClosure()
    {
        $token = $this->getTargetToken('/* testClosure */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 60, 'comma');
    }
    //end testClosure()
    /**
     * Test arrow functions using an array index.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testArrayIndex()
    {
        $token = $this->getTargetToken('/* testArrayIndex */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 17, 'comma');
    }
    //end testArrayIndex()
    /**
     * Test arrow functions with a return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReturnType()
    {
        $token = $this->getTargetToken('/* testReturnType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18, 'comma');
    }
    //end testReturnType()
    /**
     * Test arrow functions that return a reference.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReference()
    {
        $token = $this->getTargetToken('/* testReference */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 6, 9);
    }
    //end testReference()
    /**
     * Test arrow functions that are grouped by parenthesis.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testGrouped()
    {
        $token = $this->getTargetToken('/* testGrouped */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 8);
    }
    //end testGrouped()
    /**
     * Test arrow functions that are used as array values.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testArrayValue()
    {
        $token = $this->getTargetToken('/* testArrayValue */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 9, 'comma');
    }
    //end testArrayValue()
    /**
     * Test arrow functions that are used as array values with no trailing comma.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testArrayValueNoTrailingComma()
    {
        $token = $this->getTargetToken('/* testArrayValueNoTrailingComma */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 8, 'closing parenthesis');
    }
    //end testArrayValueNoTrailingComma()
    /**
     * Test arrow functions that use the yield keyword.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testYield()
    {
        $token = $this->getTargetToken('/* testYield */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 14);
    }
    //end testYield()
    /**
     * Test arrow functions that use nullable namespace types.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNullableNamespace()
    {
        $token = $this->getTargetToken('/* testNullableNamespace */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 15, 18);
    }
    //end testNullableNamespace()
    /**
     * Test arrow functions that use the namespace operator in the return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNamespaceOperatorInTypes()
    {
        $token = $this->getTargetToken('/* testNamespaceOperatorInTypes */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 16, 19);
    }
    //end testNamespaceOperatorInTypes()
    /**
     * Test arrow functions that use keyword return types.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataKeywordReturnTypes
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testKeywordReturnTypes($testMarker)
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken($testMarker, \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 14);
    }
    //end testKeywordReturnTypes()
    /**
     * Data provider.
     *
     * @see testKeywordReturnTypes()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataKeywordReturnTypes()
    {
        return ['self' => ['testMarker' => '/* testSelfReturnType */'], 'parent' => ['testMarker' => '/* testParentReturnType */'], 'callable' => ['testMarker' => '/* testCallableReturnType */'], 'array' => ['testMarker' => '/* testArrayReturnType */'], 'static' => ['testMarker' => '/* testStaticReturnType */'], 'false' => ['testMarker' => '/* testFalseReturnType */'], 'true' => ['testMarker' => '/* testTrueReturnType */'], 'null' => ['testMarker' => '/* testNullReturnType */']];
    }
    //end dataKeywordReturnTypes()
    /**
     * Test arrow function with a union parameter type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionParamType()
    {
        $token = $this->getTargetToken('/* testUnionParamType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 13, 21);
    }
    //end testUnionParamType()
    /**
     * Test arrow function with a union return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionReturnType()
    {
        $token = $this->getTargetToken('/* testUnionReturnType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18);
    }
    //end testUnionReturnType()
    /**
     * Test arrow function with a union return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionReturnTypeWithTrue()
    {
        $token = $this->getTargetToken('/* testUnionReturnTypeWithTrue */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18);
    }
    //end testUnionReturnTypeWithTrue()
    /**
     * Test arrow function with a union return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionReturnTypeWithFalse()
    {
        $token = $this->getTargetToken('/* testUnionReturnTypeWithFalse */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18);
    }
    //end testUnionReturnTypeWithFalse()
    /**
     * Test arrow function with an intersection parameter type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testIntersectionParamType()
    {
        $token = $this->getTargetToken('/* testIntersectionParamType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 13, 27);
    }
    //end testIntersectionParamType()
    /**
     * Test arrow function with an intersection return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testIntersectionReturnType()
    {
        $token = $this->getTargetToken('/* testIntersectionReturnType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 12, 20);
    }
    //end testIntersectionReturnType()
    /**
     * Test arrow function with a DNF parameter type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testDNFParamType()
    {
        $token = $this->getTargetToken('/* testDNFParamType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 17, 29);
    }
    //end testDNFParamType()
    /**
     * Test arrow function with a DNF return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testDNFReturnType()
    {
        $token = $this->getTargetToken('/* testDNFReturnType */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 16, 29);
    }
    //end testDNFReturnType()
    /**
     * Test arrow function which returns by reference with a DNF parameter type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testDNFParamTypeWithReturnByRef()
    {
        $token = $this->getTargetToken('/* testDNFParamTypeWithReturnByRef */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 15, 22);
    }
    //end testDNFParamTypeWithReturnByRef()
    /**
     * Test arrow functions used in ternary operators.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTernary()
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken('/* testTernary */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 40);
        $token = $this->getTargetToken('/* testTernaryThen */', \T_FN);
        $this->backfillHelper($token);
        $expectedScopeOpener = $token + 8;
        $expectedScopeCloser = $token + 12;
        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer for THEN is not the semicolon token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer for THEN is not the semicolon token');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], 'Closer scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer for THEN is not the semicolon token');
        $token = $this->getTargetToken('/* testTernaryElse */', \T_FN);
        $this->backfillHelper($token, \true);
        $expectedScopeOpener = $token + 8;
        $expectedScopeCloser = $token + 11;
        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener for ELSE is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer for ELSE is not the semicolon token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener for ELSE is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer for ELSE is not the semicolon token');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($token - 24, $tokens[$closer]['scope_opener'], 'Closer scope opener for ELSE is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer for ELSE is not the semicolon token');
    }
    //end testTernary()
    /**
     * Test typed arrow functions used in ternary operators.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTernaryWithTypes()
    {
        $token = $this->getTargetToken('/* testTernaryWithTypes */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 15, 27);
    }
    //end testTernaryWithTypes()
    /**
     * Test arrow function returning a match control structure.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWithMatchValue()
    {
        $token = $this->getTargetToken('/* testWithMatchValue */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 44);
    }
    //end testWithMatchValue()
    /**
     * Test arrow function returning a match control structure with something behind it.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWithMatchValueAndMore()
    {
        $token = $this->getTargetToken('/* testWithMatchValueAndMore */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 48);
    }
    //end testWithMatchValueAndMore()
    /**
     * Test match control structure returning arrow functions.
     *
     * @param string $testMarker                 The comment prefacing the target token.
     * @param int    $openerOffset               The expected offset of the scope opener in relation to the testMarker.
     * @param int    $closerOffset               The expected offset of the scope closer in relation to the testMarker.
     * @param string $expectedCloserType         The type of token expected for the scope closer.
     * @param string $expectedCloserFriendlyName A friendly name for the type of token expected for the scope closer
     *                                           to be used in the error message for failing tests.
     *
     * @dataProvider dataInMatchValue
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testInMatchValue($testMarker, $openerOffset, $closerOffset, $expectedCloserType, $expectedCloserFriendlyName)
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken($testMarker, \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, $openerOffset, $closerOffset, $expectedCloserFriendlyName);
        $this->assertSame($expectedCloserType, $tokens[$token + $closerOffset]['type'], 'Mismatched scope closer type');
    }
    //end testInMatchValue()
    /**
     * Data provider.
     *
     * @see testInMatchValue()
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataInMatchValue()
    {
        return ['not_last_value' => ['testMarker' => '/* testInMatchNotLastValue */', 'openerOffset' => 5, 'closerOffset' => 11, 'expectedCloserType' => 'T_COMMA', 'expectedCloserFriendlyName' => 'comma'], 'last_value_with_trailing_comma' => ['testMarker' => '/* testInMatchLastValueWithTrailingComma */', 'openerOffset' => 5, 'closerOffset' => 11, 'expectedCloserType' => 'T_COMMA', 'expectedCloserFriendlyName' => 'comma'], 'last_value_without_trailing_comma_1' => ['testMarker' => '/* testInMatchLastValueNoTrailingComma1 */', 'openerOffset' => 5, 'closerOffset' => 10, 'expectedCloserType' => 'T_CLOSE_PARENTHESIS', 'expectedCloserFriendlyName' => 'close parenthesis'], 'last_value_without_trailing_comma_2' => ['testMarker' => '/* testInMatchLastValueNoTrailingComma2 */', 'openerOffset' => 5, 'closerOffset' => 11, 'expectedCloserType' => 'T_VARIABLE', 'expectedCloserFriendlyName' => '$y variable']];
    }
    //end dataInMatchValue()
    /**
     * Test arrow function nested within a method declaration.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedInMethod()
    {
        $token = $this->getTargetToken('/* testNestedInMethod */', \T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 17);
    }
    //end testNestedInMethod()
    /**
     * Verify that "fn" keywords which are not arrow functions get tokenized as T_STRING and don't
     * have the extra token array indexes.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotAnArrowFunction
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNotAnArrowFunction($testMarker, $testContent = 'fn')
    {
        $tokens = $this->phpcsFile->getTokens();
        $token = $this->getTargetToken($testMarker, [\T_STRING, \T_FN], $testContent);
        $tokenArray = $tokens[$token];
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as ' . $tokenArray['type'] . ', not T_STRING');
        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');
        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');
    }
    //end testNotAnArrowFunction()
    /**
     * Data provider.
     *
     * @see testNotAnArrowFunction()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotAnArrowFunction()
    {
        return ['name of a function, context: declaration' => ['testMarker' => '/* testFunctionName */'], 'name of a constant, context: declaration using "const" keyword - uppercase' => ['testMarker' => '/* testConstantDeclaration */', 'testContent' => 'FN'], 'name of a constant, context: declaration using "const" keyword - lowercase' => ['testMarker' => '/* testConstantDeclarationLower */', 'testContent' => 'fn'], 'name of a (static) method, context: declaration' => ['testMarker' => '/* testStaticMethodName */'], 'name of a property, context: property access' => ['testMarker' => '/* testPropertyAssignment */'], 'name of a method, context: declaration in an anon class - mixed case' => ['testMarker' => '/* testAnonClassMethodName */', 'testContent' => 'fN'], 'name of a method, context: static method call' => ['testMarker' => '/* testNonArrowStaticMethodCall */'], 'name of a constant, context: constant access - uppercase' => ['testMarker' => '/* testNonArrowConstantAccess */', 'testContent' => 'FN'], 'name of a constant, context: constant access - mixed case' => ['testMarker' => '/* testNonArrowConstantAccessMixed */', 'testContent' => 'Fn'], 'name of a method, context: method call on object - lowercase' => ['testMarker' => '/* testNonArrowObjectMethodCall */'], 'name of a method, context: method call on object - uppercase' => ['testMarker' => '/* testNonArrowObjectMethodCallUpper */', 'testContent' => 'FN'], 'name of a (namespaced) function, context: partially qualified function call' => ['testMarker' => '/* testNonArrowNamespacedFunctionCall */', 'testContent' => 'Fn'], 'name of a (namespaced) function, context: namespace relative function call' => ['testMarker' => '/* testNonArrowNamespaceOperatorFunctionCall */'], 'name of a function, context: declaration with union types for param and return' => ['testMarker' => '/* testNonArrowFunctionNameWithUnionTypes */'], 'unknown - live coding/parse error' => ['testMarker' => '/* testLiveCoding */']];
    }
    //end dataNotAnArrowFunction()
    /**
     * Helper function to check that all token keys are correctly set for T_FN tokens.
     *
     * @param int  $token                The T_FN token to check.
     * @param bool $skipScopeCloserCheck Whether to skip the scope closer check.
     *                                   This should be set to "true" when testing nested arrow functions,
     *                                   where the "inner" arrow function shares a scope closer with the
     *                                   "outer" arrow function, as the 'scope_condition' for the scope closer
     *                                   of the "inner" arrow function will point to the "outer" arrow function.
     *
     * @return void
     */
    private function backfillHelper($token, $skipScopeCloserCheck = \false)
    {
        $tokens = $this->phpcsFile->getTokens();
        $this->assertTrue(\array_key_exists('scope_condition', $tokens[$token]), 'Scope condition is not set');
        $this->assertTrue(\array_key_exists('scope_opener', $tokens[$token]), 'Scope opener is not set');
        $this->assertTrue(\array_key_exists('scope_closer', $tokens[$token]), 'Scope closer is not set');
        $this->assertSame($tokens[$token]['scope_condition'], $token, 'Scope condition is not the T_FN token');
        $this->assertTrue(\array_key_exists('parenthesis_owner', $tokens[$token]), 'Parenthesis owner is not set');
        $this->assertTrue(\array_key_exists('parenthesis_opener', $tokens[$token]), 'Parenthesis opener is not set');
        $this->assertTrue(\array_key_exists('parenthesis_closer', $tokens[$token]), 'Parenthesis closer is not set');
        $this->assertSame($tokens[$token]['parenthesis_owner'], $token, 'Parenthesis owner is not the T_FN token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertTrue(\array_key_exists('scope_condition', $tokens[$opener]), 'Opener scope condition is not set');
        $this->assertTrue(\array_key_exists('scope_opener', $tokens[$opener]), 'Opener scope opener is not set');
        $this->assertTrue(\array_key_exists('scope_closer', $tokens[$opener]), 'Opener scope closer is not set');
        $this->assertSame($tokens[$opener]['scope_condition'], $token, 'Opener scope condition is not the T_FN token');
        $this->assertSame(\T_FN_ARROW, $tokens[$opener]['code'], 'Arrow scope opener not tokenized as T_FN_ARROW (code)');
        $this->assertSame('T_FN_ARROW', $tokens[$opener]['type'], 'Arrow scope opener not tokenized as T_FN_ARROW (type)');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertTrue(\array_key_exists('scope_condition', $tokens[$closer]), 'Closer scope condition is not set');
        $this->assertTrue(\array_key_exists('scope_opener', $tokens[$closer]), 'Closer scope opener is not set');
        $this->assertTrue(\array_key_exists('scope_closer', $tokens[$closer]), 'Closer scope closer is not set');
        if ($skipScopeCloserCheck === \false) {
            $this->assertSame($tokens[$closer]['scope_condition'], $token, 'Closer scope condition is not the T_FN token');
        }
        $opener = $tokens[$token]['parenthesis_opener'];
        $this->assertTrue(\array_key_exists('parenthesis_owner', $tokens[$opener]), 'Opening parenthesis owner is not set');
        $this->assertSame($tokens[$opener]['parenthesis_owner'], $token, 'Opening parenthesis owner is not the T_FN token');
        $closer = $tokens[$token]['parenthesis_closer'];
        $this->assertTrue(\array_key_exists('parenthesis_owner', $tokens[$closer]), 'Closing parenthesis owner is not set');
        $this->assertSame($tokens[$closer]['parenthesis_owner'], $token, 'Closing parenthesis owner is not the T_FN token');
    }
    //end backfillHelper()
    /**
     * Helper function to check that the scope opener/closer positions are correctly set for T_FN tokens.
     *
     * @param int    $token              The T_FN token to check.
     * @param int    $openerOffset       The expected offset of the scope opener in relation to
     *                                   the fn keyword.
     * @param int    $closerOffset       The expected offset of the scope closer in relation to
     *                                   the fn keyword.
     * @param string $expectedCloserType Optional. The type of token expected for the scope closer.
     *
     * @return void
     */
    private function scopePositionTestHelper($token, $openerOffset, $closerOffset, $expectedCloserType = 'semicolon')
    {
        $tokens = $this->phpcsFile->getTokens();
        $expectedScopeOpener = $token + $openerOffset;
        $expectedScopeCloser = $token + $closerOffset;
        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer is not the ' . $expectedCloserType . ' token');
        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer is not the ' . $expectedCloserType . ' token');
        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], 'Closer scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer is not the ' . $expectedCloserType . ' token');
    }
    //end scopePositionTestHelper()
}
//end class
