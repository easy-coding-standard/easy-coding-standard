<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Configuration\Exception\ConflictingCheckersLoadedException;

final class ConflictingCheckersCompilerPass implements CompilerPassInterface
{
    /**
     * These groups do the opposite of each other, e.g. Yoda vs NoYoda.
     *
     * @var string[][]
     */
    private static $conflictingCheckerGroups = [
        [
            'SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff',
            'PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer',
        ], [
            'PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer',
            'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer',
        ], [
            'Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff',
            'PhpCsFixer\Fixer\Operator\NewWithBracesFixer',
        ], [
            'SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff',
            'PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer',
        ], [
            'SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff',
            'PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer',
        ],
    ];

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->processCheckers($containerBuilder->getServiceIds());
    }

    /**
     * @param mixed[] $checkers
     */
    private function processCheckers(array $checkers): void
    {
        if (! count($checkers)) {
            return;
        }

        $checkers = $this->makeCheckersAsKeys($checkers);

        foreach (self::$conflictingCheckerGroups as $viceVersaMatchingCheckerGroup) {
            if (! $this->isMatch($checkers, $viceVersaMatchingCheckerGroup)) {
                continue;
            }

            throw new ConflictingCheckersLoadedException(sprintf(
                'Checkers "%s" mutually exclude each other. Use only one or exclude '
                . 'the unwanted one in "parameters > exclude_checkers" in your config.',
                implode('" and "', $viceVersaMatchingCheckerGroup)
            ));
        }
    }

    /**
     * @param mixed[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        $matchingCheckerGroupKeys = $this->makeCheckersAsKeys($matchingCheckerGroup);

        return count(array_intersect_key($matchingCheckerGroupKeys, $checkers)) === count($matchingCheckerGroup);
    }

    /**
     * @param string[] $checkers
     * @return string[]
     */
    private function makeCheckersAsKeys(array $checkers): array
    {
        return isset($checkers[0]) ? (array) array_flip($checkers) : $checkers;
    }
}
