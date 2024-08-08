<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use ECSPrefix202408\Illuminate\Container\Container;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\UpperCaseConstantSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException;
final class ConflictingCheckersCompilerPass
{
    /**
     * These groups do the opposite of each other, e.g. Yoda vs NoYoda.
     *
     * @var string[][]
     */
    private const CONFLICTING_CHECKER_GROUPS = [[StandaloneLineConstructorParamFixer::class, StandaloneLinePromotedPropertyFixer::class], ['ECSPrefix202408\\SlevomatCodingStandard\\Sniffs\\ControlStructures\\DisallowYodaComparisonSniff', YodaStyleFixer::class], [LowerCaseConstantSniff::class, UpperCaseConstantSniff::class], [ConstantCaseFixer::class, UpperCaseConstantSniff::class], ['ECSPrefix202408\\SlevomatCodingStandard\\Sniffs\\TypeHints\\DeclareStrictTypesSniff', DeclareEqualNormalizeFixer::class], ['ECSPrefix202408\\SlevomatCodingStandard\\Sniffs\\TypeHints\\DeclareStrictTypesSniff', BlankLineAfterOpeningTagFixer::class], [FileHeaderSniff::class, NoBlankLinesAfterPhpdocFixer::class]];
    public function process(Container $container) : void
    {
        $checkerTypes = \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CompilerPassHelper::resolveCheckerClasses($container);
        if ($checkerTypes === []) {
            return;
        }
        foreach (self::CONFLICTING_CHECKER_GROUPS as $viceVersaMatchingCheckerGroup) {
            if (!$this->isMatch($checkerTypes, $viceVersaMatchingCheckerGroup)) {
                continue;
            }
            throw new ConflictingCheckersLoadedException(\sprintf('Checkers "%s" mutually exclude each other. Use only one of them or exclude the unwanted one in "$ecsConfig->skip(...)" in your config.', \implode('" and "', $viceVersaMatchingCheckerGroup)));
        }
    }
    /**
     * @param mixed[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup) : bool
    {
        $checkers = \array_flip($checkers);
        $matchingCheckerGroup = \array_flip($matchingCheckerGroup);
        $foundCheckers = \array_intersect_key($matchingCheckerGroup, $checkers);
        return \count($foundCheckers) === \count($matchingCheckerGroup);
    }
}
