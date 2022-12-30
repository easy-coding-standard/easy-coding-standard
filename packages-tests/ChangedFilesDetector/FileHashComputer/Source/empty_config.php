<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(CombineConsecutiveIssetsFixer::class);
};
