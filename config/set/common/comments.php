<?php

declare (strict_types=1);
namespace ECSPrefix202501;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return ECSConfig::configure()->withRules([GitMergeConflictSniff::class]);
