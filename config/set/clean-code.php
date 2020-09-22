<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\RequireShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Operators\RequireCombinedAssignmentOperatorSniff;
use SlevomatCodingStandard\Sniffs\PHP\DisallowDirectMagicInvokeCallSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DisallowDirectMagicInvokeCallSniff::class);

    $services->set(ParamReturnAndVarTagMalformsFixer::class);

    $services->set(UnusedVariableSniff::class);

    $services->set(UselessVariableSniff::class);

    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);

    $services->set(UselessSemicolonSniff::class);

    $services->set(UselessParenthesesSniff::class);

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [['syntax' => 'short']]);

    $services->set(UnusedPrivateElementsSniff::class);

    $services->set(NoUnusedImportsFixer::class);

    $services->set(OrderedImportsFixer::class);

    $services->set(NoEmptyStatementFixer::class);

    $services->set(ProtectedToPrivateFixer::class);

    $services->set(NoUnneededControlParenthesesFixer::class);

    $services->set(NoUnneededCurlyBracesFixer::class);

    $services->set(ReturnAssignmentFixer::class);

    $services->set(RequireShortTernaryOperatorSniff::class);

    $services->set(RequireCombinedAssignmentOperatorSniff::class);
};
