<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\UpperCaseConstantSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException;
final class ConflictingCheckersCompilerPass implements \ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * These groups do the opposite of each other, e.g. Yoda vs NoYoda.
     *
     * @var string[][]
     */
    private const CONFLICTING_CHECKER_GROUPS = [['ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\ControlStructures\\DisallowYodaComparisonSniff', \PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class], [\PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\UpperCaseConstantSniff::class], [\PhpCsFixer\Fixer\Casing\ConstantCaseFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\UpperCaseConstantSniff::class], ['ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\TypeHints\\DeclareStrictTypesSniff', \PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer::class], ['ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\TypeHints\\DeclareStrictTypesSniff', \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class], [\PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff::class, \PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer::class]];
    public function process(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $checkers = $containerBuilder->getServiceIds();
        if ($checkers === []) {
            return;
        }
        foreach (self::CONFLICTING_CHECKER_GROUPS as $viceVersaMatchingCheckerGroup) {
            if (!$this->isMatch($checkers, $viceVersaMatchingCheckerGroup)) {
                continue;
            }
            throw new \Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException(\sprintf('Checkers "%s" mutually exclude each other. Use only one or exclude ' . 'the unwanted one in "parameters > skip" in your config.', \implode('" and "', $viceVersaMatchingCheckerGroup)));
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
