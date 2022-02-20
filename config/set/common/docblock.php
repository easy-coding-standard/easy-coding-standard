<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

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
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class);
    $services->set(\PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer::class);
    $services->set(\Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class)->call('configure', [['remove_inheritdoc' => \true, 'allow_mixed' => \true]]);
};
