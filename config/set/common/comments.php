<?php

declare (strict_types=1);
namespace ECSPrefix20220607;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rule(GitMergeConflictSniff::class);
};
