<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\PackageBuilder\Diff;

use ECSPrefix202306\SebastianBergmann\Diff\Differ;
use ECSPrefix202306\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix202306\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
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
