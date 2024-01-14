<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([CombineConsecutiveIssetsFixer::class]);
