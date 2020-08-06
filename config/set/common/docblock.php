<?php declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PhpdocLineSpanFixer::class);

    $services->set(NoTrailingWhitespaceInCommentFixer::class);

    $services->set(PhpdocTrimConsecutiveBlankLineSeparationFixer::class);

    $services->set(PhpdocTrimFixer::class);

    $services->set(NoEmptyPhpdocFixer::class);

    $services->set(PhpdocNoEmptyReturnFixer::class);

    $services->set(PhpdocIndentFixer::class);

    $services->set(PhpdocTypesFixer::class);

    $services->set(PhpdocReturnSelfReferenceFixer::class);

    $services->set(PhpdocVarWithoutNameFixer::class);

    $services->set(NoSuperfluousPhpdocTagsFixer::class)
        ->call('configure', [
            [
                'remove_inheritdoc' => true,
                'allow_mixed' => true,
            ],
        ]);
};
