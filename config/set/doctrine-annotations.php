<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // doctrine annotations
    $services->set(DoctrineAnnotationIndentationFixer::class);
    $services->set(DoctrineAnnotationBracesFixer::class);
    $services->set(DoctrineAnnotationSpacesFixer::class);
    $services->set(DoctrineAnnotationArrayAssignmentFixer::class);
};
