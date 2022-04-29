<?php

declare (strict_types=1);
namespace ECSPrefix20220429;

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rule(\PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Strict\StrictParamFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class);
};
