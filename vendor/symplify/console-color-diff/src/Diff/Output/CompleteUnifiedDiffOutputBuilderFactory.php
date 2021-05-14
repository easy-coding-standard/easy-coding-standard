<?php

namespace ECSPrefix20210514\Symplify\ConsoleColorDiff\Diff\Output;

use ECSPrefix20210514\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use ECSPrefix20210514\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
/**
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;
    public function __construct(\ECSPrefix20210514\Symplify\PackageBuilder\Reflection\PrivatesAccessor $privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }
    /**
     * @api
     * @return \SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder
     */
    public function create()
    {
        $unifiedDiffOutputBuilder = new \ECSPrefix20210514\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder('');
        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);
        return $unifiedDiffOutputBuilder;
    }
}
