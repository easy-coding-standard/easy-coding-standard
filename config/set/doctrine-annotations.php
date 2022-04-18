<?php

declare (strict_types=1);
namespace ECSPrefix20220418;

use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer::class, ['indent_mixed_lines' => \true]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer::class, ['after_array_assignments_equals' => \false, 'before_array_assignments_equals' => \false]);
    $ecsConfig->rule(\PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer::class);
};
