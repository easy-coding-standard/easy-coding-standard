<?php

declare (strict_types=1);
namespace ECSPrefix202607;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return ECSConfig::configure()->withRules([GitMergeConflictSniff::class, NoEmptyCommentFixer::class, SingleLineCommentSpacingFixer::class, SingleLineCommentStyleFixer::class, MultilineCommentOpeningClosingFixer::class]);
