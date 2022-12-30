<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
                            SetList::CODE_QUALITY,
                            SetList::DEAD_CODE,
                            LevelSetList::UP_TO_PHP_80,
                            SetList::CODING_STYLE,
                            SetList::TYPE_DECLARATION,
                            SetList::NAMING,
                            SetList::PRIVATIZATION,
                            SetList::EARLY_RETURN,
                            PHPUnitSetList::PHPUNIT_CODE_QUALITY,
                        ]);

    $rectorConfig->ruleWithConfiguration(StringClassNameToClassConstantRector::class, [
        'Error',
        'Exception',
        'Dibi\Connection',
        'Doctrine\ORM\EntityManagerInterface',
        'Doctrine\ORM\EntityManager',
        'PHPUnit_Framework_TestCase',
        'Nette\*',
        'Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        'PHPUnit\Framework\TestCase',
        'Symplify\EasyCodingStandard\Config\ECSConfig',
        'Symfony\Contracts\Service\Attribute\Required',
        'Symfony\Component\Routing\Annotation\Route',
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'Rector\Config\RectorConfig',
        'Symplify\PackageBuilder\Reflection\PrivatesCaller',
        'Symfony\Component\Form\AbstractType',
    ]);

    $rectorConfig->paths([__DIR__ . '/packages']);
    $rectorConfig->parallel();
    $rectorConfig->importNames();

    $rectorConfig->autoloadPaths([__DIR__ . '/tests/bootstrap.php']);

    $rectorConfig->skip([
                            // deprecated, to be removed
                            __DIR__ . '/packages/phpstan-rules/packages/CognitiveComplexity',

                            '*/scoper.php',
                            '*/vendor/*',
                            '*/init/*',
                            '*/Source/*',
                            '*/Fixture/*',
                            '*/Fixture*/*',
                            '*/ChangedFilesDetectorSource/*',
                            __DIR__ . '/packages/monorepo-builder/templates',
                            // test fixtures
                            '*/packages/phpstan-extensions/tests/TypeExtension/*/*Extension/data/*',
                            __DIR__ . '/packages/phpstan-rules/build/*',

                            // many false positives related to file class autoload
                            __DIR__ . '/packages/easy-coding-standard/bin/ecs.php',

                            // false positive on "locale" string
                            VarConstantCommentRector::class => [
                                __DIR__ . '/packages/php-config-printer/src/RoutingCaseConverter/ImportRoutingCaseConverter.php',
                            ],

                            // keep classes untouched, to avoid prefixing and renames
                            StringClassNameToClassConstantRector::class => [
                                __DIR__ . '/packages/phpstan-rules/src/NodeAnalyzer/MethodCall/AllowedChainCallSkipper.php',
                                __DIR__ . '/packages/autowire-array-parameter/src/DependencyInjection/CompilerPass/AutowireArrayParameterCompilerPass.php',
                            ],

                            \Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector::class => [
                                __DIR__ . '/packages/phpstan-rules/src/Rules/Domain/ForbiddenAlwaysSetterCallRule.php',
                            ],

                            \Rector\Privatization\Rector\Property\ChangeReadOnlyPropertyWithDefaultValueToConstantRector::class => [
                                __DIR__ . '/packages/autowire-array-parameter/src/DependencyInjection/CompilerPass/AutowireArrayParameterCompilerPass.php',
                            ],
                        ]);
};
