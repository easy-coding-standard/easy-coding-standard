<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/php_cs_fixer/php-cs-fixer-psr2.php');

    $services = $containerConfigurator->services();

    $services->set(LowercaseCastFixer::class);

    $services->set(ShortScalarCastFixer::class);

    $services->set(BlankLineAfterOpeningTagFixer::class);

    $services->set(NoLeadingImportSlashFixer::class);

    $services->set(OrderedImportsFixer::class)
        ->call('configure', [['importsOrder' => ['class', 'function', 'const']]]);

    $services->set(DeclareEqualNormalizeFixer::class)
        ->call('configure', [['space' => 'none']]);

    $services->set(NewWithBracesFixer::class);

    $services->set(BracesFixer::class)
        ->call('configure', [['allow_single_line_closure' => false, 'position_after_functions_and_oop_constructs' => 'next', 'position_after_control_structures' => 'same', 'position_after_anonymous_constructs' => 'same']]);

    $services->set(NoBlankLinesAfterClassOpeningFixer::class);

    $services->set(VisibilityRequiredFixer::class)
        ->call('configure', [['elements' => ['const', 'method', 'property']]]);

    $services->set(BinaryOperatorSpacesFixer::class);

    $services->set(TernaryOperatorSpacesFixer::class);

    $services->set(UnaryOperatorSpacesFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(NoTrailingWhitespaceFixer::class);

    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'one']]);

    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);

    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);

    $services->set(WhitespaceAfterCommaInArrayFixer::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [SingleImportPerStatementFixer::class => null]);
};
