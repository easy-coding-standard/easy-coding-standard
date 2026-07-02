<?php

declare (strict_types=1);
namespace ECSPrefix202607;

use Symplify\EasyCodingStandard\Config\ECSConfig;
\trigger_error('The "symplify" set is deprecated. Its rules now live in the "common" sets - use ->withPreparedSets(common: true) or the matching ->withDocblockLevel()/->withSpacesLevel()/->withArrayLevel() methods instead.', \E_USER_DEPRECATED);
return ECSConfig::configure()->withRules([]);
