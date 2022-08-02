<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Annotation;

use ECSPrefix202208\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token as DoctrineAnnotationToken;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver;
use ECSPrefix202208\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix202208\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix202208\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use ECSPrefix202208\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use ECSPrefix202208\Webmozart\Assert\Assert;
final class DoctrineAnnotationNestedBracketsFixer extends AbstractSymplifyFixer implements ConfigurableRuleInterface, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ANNOTATION_CLASSES = 'annotation_classes';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Adds nested curly brackets to defined annotations, see https://github.com/doctrine/annotations/issues/418';
    /**
     * @var string[]
     */
    private $annotationClasses = [];
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer
     */
    private $doctrineAnnotationElementAnalyzer;
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver
     */
    private $doctrineAnnotationNameResolver;
    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;
    public function __construct(DoctrineAnnotationElementAnalyzer $doctrineAnnotationElementAnalyzer, DoctrineAnnotationNameResolver $doctrineAnnotationNameResolver, NamespaceUsesAnalyzer $namespaceUsesAnalyzer)
    {
        $this->doctrineAnnotationElementAnalyzer = $doctrineAnnotationElementAnalyzer;
        $this->doctrineAnnotationNameResolver = $doctrineAnnotationNameResolver;
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;
    }
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
/**
* @MainAnnotation(
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* )
*/
CODE_SAMPLE
, <<<'CODE_SAMPLE'
/**
* @MainAnnotation({
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* })
*/
CODE_SAMPLE
, [self::ANNOTATION_CLASSES => ['MainAnnotation']])]);
    }
    /**
     * @param array<string, string[]> $configuration
     */
    public function configure(array $configuration) : void
    {
        $annotationsClasses = $configuration[self::ANNOTATION_CLASSES] ?? [];
        Assert::isArray($annotationsClasses);
        Assert::allString($annotationsClasses);
        $this->annotationClasses = $annotationsClasses;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens) : void
    {
        $useDeclarations = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);
        // fetch indexes one time, this is safe as we never add or remove a token during fixing
        /** @var Token[] $docCommentTokens */
        $docCommentTokens = $tokens->findGivenKind(\T_DOC_COMMENT);
        foreach ($docCommentTokens as $index => $docCommentToken) {
            /** @var DoctrineAnnotationTokens<DoctrineAnnotationToken> $doctrineAnnotationTokens */
            $doctrineAnnotationTokens = DoctrineAnnotationTokens::createFromDocComment($docCommentToken, []);
            $this->fixAnnotations($doctrineAnnotationTokens, $useDeclarations);
            $tokens[$index] = new Token([\T_DOC_COMMENT, $doctrineAnnotationTokens->getCode()]);
        }
    }
    /**
     * @param DoctrineAnnotationTokens<DoctrineAnnotationToken> $doctrineAnnotationTokens
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fixAnnotations(DoctrineAnnotationTokens $doctrineAnnotationTokens, array $useDeclarations) : void
    {
        foreach ($doctrineAnnotationTokens as $index => $token) {
            if (!$token->isType(DocLexer::T_AT)) {
                continue;
            }
            $annotationName = $this->doctrineAnnotationNameResolver->resolveName($doctrineAnnotationTokens, $index, $useDeclarations);
            if ($annotationName === null) {
                continue;
            }
            if (!\in_array($annotationName, $this->annotationClasses, \true)) {
                continue;
            }
            $closingBraceIndex = $doctrineAnnotationTokens->getAnnotationEnd($index);
            if ($closingBraceIndex === null) {
                continue;
            }
            $braceIndex = $doctrineAnnotationTokens->getNextMeaningfulToken($index + 1);
            if ($braceIndex === null) {
                continue;
            }
            /** @var DoctrineAnnotationToken $braceToken */
            $braceToken = $doctrineAnnotationTokens[$braceIndex];
            if (!$this->doctrineAnnotationElementAnalyzer->isOpeningBracketFollowedByAnnotation($braceToken, $doctrineAnnotationTokens, $braceIndex)) {
                continue;
            }
            // add closing brace
            $doctrineAnnotationTokens->insertAt($closingBraceIndex, new DoctrineAnnotationToken(DocLexer::T_OPEN_CURLY_BRACES, '}'));
            // add opening brace
            $doctrineAnnotationTokens->insertAt($braceIndex + 1, new DoctrineAnnotationToken(DocLexer::T_OPEN_CURLY_BRACES, '{'));
        }
    }
}
