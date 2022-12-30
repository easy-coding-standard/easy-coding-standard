<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DowngradePhp74\Rector\ClassMethod\DowngradeCovariantReturnTypeRector;
use Rector\DowngradePhp80\Rector\Class_\DowngradeAttributeToAnnotationRector;
use Rector\DowngradePhp80\ValueObject\DowngradeAttributeToAnnotation;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Symfony\Contracts\Service\Attribute\Required;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();

    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);

    $rectorConfig->ruleWithConfiguration(DowngradeAttributeToAnnotationRector::class, [
        new DowngradeAttributeToAnnotation(Required::class, 'required'),
    ]);

    $rectorConfig->skip([
        '*/tests/*',
        '*/Tests/*',
        '*/tests/*',
        # missing "optional" dependency and never used here
        '*/symfony/framework-bundle/KernelBrowser.php',

        // skip for parrent type override, see https://github.com/symplify/symplify/issues/4500
        DowngradeCovariantReturnTypeRector::class => [
            'doctrine/annotations/lib/Doctrine/Common/Annotations/DocLexer.php',
        ],
    ]);
};
