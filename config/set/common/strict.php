<?php

declare (strict_types=1);
namespace ECSPrefix20220607;

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([\PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class, \PhpCsFixer\Fixer\Strict\StrictParamFixer::class, \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class]);
};
