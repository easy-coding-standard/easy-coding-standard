<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
/**
 * @deprecated This rule was split into single-task rules collected in DocblockLevel.
 *             Use DocblockLevel or the dedicated rules instead.
 */
final class ParamReturnAndVarTagMalformsFixer extends AbstractSymplifyFixer implements DeprecatedFixerInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Fixes @param, @return, @var and inline @var annotations broken formats';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return \false;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens): void
    {
    }
    /**
     * @return list<string>
     */
    public function getSuccessorsNames(): array
    {
        return [\Symplify\CodingStandard\Fixer\Commenting\DoubleAsteriskInlineVarFixer::class, \Symplify\CodingStandard\Fixer\Commenting\SingleLineInlineVarDocBlockFixer::class, \Symplify\CodingStandard\Fixer\Commenting\AddMissingParamNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\AddMissingVarNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\RemoveParamNameReferenceFixer::class, \Symplify\CodingStandard\Fixer\Commenting\FixParamNameTypoFixer::class, \Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousReturnNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousVarNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\SwitchedTypeAndNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\RemoveDeadParamFixer::class];
    }
}
