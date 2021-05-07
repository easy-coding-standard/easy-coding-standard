<?php

namespace Symplify\ConsoleColorDiff\Diff\Output;

use ECSPrefix20210507\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
/**
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;
    /**
     * @param \Symplify\PackageBuilder\Reflection\PrivatesAccessor $privatesAccessor
     */
    public function __construct($privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }
    /**
     * @api
     * @return \SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder
     */
    public function create()
    {
        $unifiedDiffOutputBuilder = new \ECSPrefix20210507\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder('');
        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);
        return $unifiedDiffOutputBuilder;
    }
}
