<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DoctrineAnnotationArrayAssignmentFixer::class)
        ->call('configure', [['operator' => ':']]);
    $services->set(DoctrineAnnotationBracesFixer::class);
    $services->set(DoctrineAnnotationIndentationFixer::class);
    $services->set(DoctrineAnnotationNewlineInNestedAnnotationFixer::class);
    $services->set(DoctrineAnnotationSpacesFixer::class)
        ->call('configure', [['before_array_assignments_colon' => false]]);
};
