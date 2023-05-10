<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\PackageBuilder\Diff;

use ECSPrefix202305\SebastianBergmann\Diff\Differ;
use ECSPrefix202305\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix202305\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
final class DifferFactory
{
    /**
     * @api
     */
    public function create() : Differ
    {
        $completeUnifiedDiffOutputBuilderFactory = new CompleteUnifiedDiffOutputBuilderFactory(new PrivatesAccessor());
        $unifiedDiffOutputBuilder = $completeUnifiedDiffOutputBuilderFactory->create();
        return new Differ($unifiedDiffOutputBuilder);
    }
}
