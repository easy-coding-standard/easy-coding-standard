<?php

declare (strict_types=1);
namespace ECSPrefix20220605;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rule(\PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff::class);
};
