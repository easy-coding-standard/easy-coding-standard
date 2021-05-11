<?php

namespace Symplify\CodingStandard\Fixer\LineLength;

use ECSPrefix20210511\Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\ValueObjectFactory\DocBlockLinesFactory;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\DocBlockLineLengthFixer\DocBlockLineLengthFixerTest
 */
final class DocBlockLineLengthFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    const LINE_LENGTH = 'line_length';
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Docblock lenght should fit expected width';
    /**
     * @see https://regex101.com/r/DNWfB6/1
     * @var string
     */
    const INDENTATION_BEFORE_ASTERISK_REGEX = '/^(?<' . self::INDENTATION_PART . '>\\s*) \\*/m';
    /**
     * @var string
     */
    const INDENTATION_PART = 'indentation_part';
    /**
     * @var int
     */
    const DEFAULT_LINE_LENGHT = 120;
    /**
     * @var int
     */
    private $lineLength = self::DEFAULT_LINE_LENGHT;
    /**
     * @var DocBlockLinesFactory
     */
    private $docBlockLinesFactory;
    public function __construct(\Symplify\CodingStandard\ValueObjectFactory\DocBlockLinesFactory $docBlockLinesFactory)
    {
        $this->docBlockLinesFactory = $docBlockLinesFactory;
    }
    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        // function arguments, function call parameters, lambda use()
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $docBlock = $token->getContent();
            $docBlockLines = $this->docBlockLinesFactory->createFromDocBlock($docBlock);
            // The available line length is the configured line length, minus the existing indentation, minus ' * '
            $indentationString = $this->resolveIndentationStringFor($docBlock);
            $maximumLineLength = $this->lineLength - \strlen($indentationString) - 3;
            $descriptionLines = $docBlockLines->getDescriptionLines();
            if ($descriptionLines === []) {
                continue;
            }
            if ($docBlockLines->hasListDescriptionLines()) {
                continue;
            }
            $paragraphs = $this->extractParagraphsFromDescriptionLines($descriptionLines);
            $lineWrappedParagraphs = $this->wrapParagraphs($paragraphs, $maximumLineLength);
            $wrappedDescription = \implode(\PHP_EOL . \PHP_EOL, $lineWrappedParagraphs);
            $otherLines = $docBlockLines->getOtherLines();
            if ($otherLines !== []) {
                $wrappedDescription .= "\n";
            }
            $reformattedLines = \array_merge($this->getLines($wrappedDescription), $otherLines);
            $newDocBlockContent = $this->formatLinesAsDocBlockContent($reformattedLines, $indentationString);
            if ($docBlock === $newDocBlockContent) {
                continue;
            }
            $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $newDocBlockContent]);
        }
    }
    /**
     * @param mixed[]|null $configuration
     * @return void
     */
    public function configure($configuration = null)
    {
        $this->lineLength = isset($configuration[self::LINE_LENGTH]) ? $configuration[self::LINE_LENGTH] : self::DEFAULT_LINE_LENGHT;
    }
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new \Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
/**
 * Super long doc block description
 */
function some()
{
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
/**
 * Super long doc
 * block description
 */
function some()
{
}
CODE_SAMPLE
, [self::LINE_LENGTH => 40])]);
    }
    /**
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    public function getConfigurationDefinition()
    {
        throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    /**
     * @param string $docBlock
     * @return string
     */
    private function resolveIndentationStringFor($docBlock)
    {
        $docBlock = (string) $docBlock;
        $matches = \ECSPrefix20210511\Nette\Utils\Strings::match($docBlock, self::INDENTATION_BEFORE_ASTERISK_REGEX);
        return isset($matches[self::INDENTATION_PART]) ? $matches[self::INDENTATION_PART] : '';
    }
    /**
     * @param string $indentationString
     * @return string
     */
    private function formatLinesAsDocBlockContent(array $docBlockLines, $indentationString)
    {
        $indentationString = (string) $indentationString;
        foreach ($docBlockLines as $index => $docBlockLine) {
            $docBlockLines[$index] = $indentationString . ' *' . ($docBlockLine !== '' ? ' ' : '') . $docBlockLine;
        }
        \array_unshift($docBlockLines, '/**');
        $docBlockLines[] = $indentationString . ' */';
        return \implode(\PHP_EOL, $docBlockLines);
    }
    /**
     * @return mixed[]
     */
    private function extractParagraphsFromDescriptionLines(array $descriptionLines)
    {
        $paragraphLines = [];
        $paragraphIndex = 0;
        foreach ($descriptionLines as $line) {
            if (!isset($paragraphLines[$paragraphIndex])) {
                $paragraphLines[$paragraphIndex] = [];
            }
            $line = \trim($line);
            if ($line === '') {
                ++$paragraphIndex;
            } else {
                $paragraphLines[$paragraphIndex][] = $line;
            }
        }
        return \array_map(function (array $lines) : string {
            return \implode(' ', $lines);
        }, $paragraphLines);
    }
    /**
     * @return mixed[]
     * @param string $string
     */
    private function getLines($string)
    {
        $string = (string) $string;
        return \explode(\PHP_EOL, $string);
    }
    /**
     * @param string[] $lines
     * @return mixed[]
     * @param int $maximumLineLength
     */
    private function wrapParagraphs(array $lines, $maximumLineLength)
    {
        $maximumLineLength = (int) $maximumLineLength;
        $wrappedLines = [];
        foreach ($lines as $line) {
            $wrappedLines[] = \wordwrap($line, $maximumLineLength);
        }
        return $wrappedLines;
    }
}
